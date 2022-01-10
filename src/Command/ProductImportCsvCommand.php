<?php

namespace App\Command;

use App\Assistants\Reader\ReaderContract;
use App\Components\Product\Entities\ProductReadonlyContract;
use App\Components\Product\Services\ProductServiceContract;
use App\Convention\Services\Doctrine\Flusher;
use DateTime;
use Exception;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Validator\Constraints as Assert;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Exception\ValidatorException;
use Symfony\Component\Validator\Validation;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Throwable;

/**
 * Class ProductImportCsvCommand
 *
 * @package App\Command
 */
#[AsCommand(name: 'app:product:import:csv', description: 'Add a short description for your command',)]
class ProductImportCsvCommand extends Command
{
    private const ARGUMENT_FILE = 'file';

    private const ARGUMENT_TEST = 'test';

    #region CSV COLUMN NAMES
    private const COLUMN_PRODUCT_CODE = 'Product Code';

    private const COLUMN_PRODUCT_NAME = 'Product Name';

    private const COLUMN_PRODUCT_DESCRIPTION = 'Product Description';

    private const COLUMN_STOCK = 'Stock';

    private const COLUMN_COST_IN_GBP = 'Cost in GBP';

    private const COLUMN_DISCONTINUED = 'Discontinued';

    #endregion

    private const REQUIRED_COLUMNS = [
        self::COLUMN_PRODUCT_CODE,
        self::COLUMN_PRODUCT_NAME,
        self::COLUMN_PRODUCT_DESCRIPTION,
        self::COLUMN_STOCK,
        self::COLUMN_COST_IN_GBP,
        self::COLUMN_DISCONTINUED,
    ];

    #region CONSTRAINTS
    private const CONSTRAINT_MAX_PRICE = 1000;

    private const CONSTRAINT_MIN_PRICE = 5;

    private const CONSTRAINT_MIN_QUANTITY = 10;

    #endregion

    /**
     * @var ReaderContract
     */
    private ReaderContract $reader;

    /**
     * @var ProductServiceContract
     */
    private ProductServiceContract $productService;

    /**
     * @var Flusher
     */
    private Flusher $flusher;

    /**
     * @var LoggerInterface
     */
    private LoggerInterface $logger;

    /**
     * @var array
     */
    private array $header = [];

    /**
     * @param ReaderContract         $reader
     * @param ProductServiceContract $productService
     * @param Flusher                $flusher
     * @param LoggerInterface        $logger
     */
    public function __construct(
        ReaderContract $reader,
        ProductServiceContract $productService,
        Flusher $flusher,
        LoggerInterface $importLogger
    ) {
        parent::__construct();

        $this->reader = $reader;
        $this->productService = $productService;
        $this->flusher = $flusher;
        $this->logger = $importLogger;
    }

    /**
     * @inheritDoc
     */
    protected function configure(): void
    {
        $this->addArgument(self::ARGUMENT_FILE, InputArgument::REQUIRED, 'File path');
        $this->addOption(self::ARGUMENT_TEST, '-t', InputOption::VALUE_NONE, 'Test mode');
    }

    /**
     * @inheritDoc
     */
    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $output->writeln('Starting import');
            $this->flusher->open();
            $isTestMode = (bool)$input->getOption(self::ARGUMENT_TEST);
            $line = 0;
            $invalid = 0;
            $unsatisfied = 0;

            foreach ($this->reader->read($input->getArgument(self::ARGUMENT_FILE))->rows() as $row) {
                if (!$this->header) {
                    $this->validateHeader($row);
                    $this->header = $row;
                    $line++;
                    continue;
                }

                try {
                    $data = $this->validatedData($row);
                    $this->validateProduct($data);
                    $this->productService->create($data);
                } catch (ValidationFailedException $exception) {
                    $this->logger->info(
                        "The product hasn't satisfy the rules on line $line. Error: {$exception->getMessage()}"
                    );
                    $unsatisfied++;
                } catch (InvalidArgumentException | ValidatorException $exception) {
                    $this->logger->warning(
                        "The product has invalid data on line $line. Error: {$exception->getMessage()}"
                    );
                    $invalid++;
                }

                $line++;
            }

            if (!$isTestMode) {
                $this->flusher->flush();
                $this->flusher->commit();
            }

            $imported = $line - $invalid - $unsatisfied;
            $output->writeln(
                "Import was finished successfully, processed: $line, imported: $imported, skipped due to invalid format: $invalid, skipped due to unsatisfied the rules: $unsatisfied"
            );

            return Command::SUCCESS;
        } catch (Throwable $throwable) {
            $this->flusher->rollback();
            $output->writeln('Import was interrupted due to error');
            $this->logger->error($throwable->getMessage());

            return Command::FAILURE;
        }
    }

    /**
     * @param array $header
     *
     * @return void
     */
    private function validateHeader(array $header): void
    {
        if (count(self::REQUIRED_COLUMNS) !== count(array_intersect(self::REQUIRED_COLUMNS, $header))) {
            throw new ValidatorException(
                'Header is invalid, required fields: ' . implode(', ', self::REQUIRED_COLUMNS)
            );
        }
    }

    /**
     * @param array|mixed $data
     *
     * @return array
     */
    private function validatedData($data): array
    {
        $this->validateRow($data);
        $row = array_combine($this->header, $data);

        return [
            ProductReadonlyContract::COLUMN_NAME            => $row[self::COLUMN_PRODUCT_NAME],
            ProductReadonlyContract::COLUMN_DESCRIPTION     => $row[self::COLUMN_PRODUCT_DESCRIPTION],
            ProductReadonlyContract::COLUMN_CODE            => $row[self::COLUMN_PRODUCT_CODE],
            ProductReadonlyContract::COLUMN_PRICE           => (float)$row[self::COLUMN_COST_IN_GBP],
            ProductReadonlyContract::COLUMN_STOCK           => (int)$row[self::COLUMN_STOCK],
            ProductReadonlyContract::COLUMN_DISCONTINUED_AT => $row[self::COLUMN_DISCONTINUED] ? new DateTime() : null,
        ];
    }

    /**
     * @param array $data
     */
    private function validateProduct(array $data): void
    {
        $validator = Validation::createValidator();
        $result = $validator->validate($data[ProductReadonlyContract::COLUMN_PRICE], [
            new Assert\Type(['type' => 'float', 'message' => 'The price {{ value }} is not a valid {{ type }}.']),
            new Assert\LessThanOrEqual([
                'value'   => self::CONSTRAINT_MAX_PRICE,
                'message' => 'The price should be less than or equal to {{ compared_value }}.',
            ]),
            new Assert\Positive(['message' => 'The price should be positive.']),
        ]);

        if ($result->count()) {
            throw new ValidationFailedException($data, $result);
        }

        $result->addAll(
            $validator->validate(
                [
                    ProductReadonlyContract::COLUMN_PRICE => $data[ProductReadonlyContract::COLUMN_PRICE],
                    ProductReadonlyContract::COLUMN_STOCK => $data[ProductReadonlyContract::COLUMN_STOCK],
                ],
                new Assert\Collection([
                    ProductReadonlyContract::COLUMN_PRICE => new Assert\GreaterThanOrEqual([
                        'value'   => self::CONSTRAINT_MIN_PRICE,
                        'message' => 'The price should be greater than or equal to {{ compared_value }}.',
                    ]),
                    ProductReadonlyContract::COLUMN_STOCK => new Assert\GreaterThanOrEqual([
                            'value'   => self::CONSTRAINT_MIN_QUANTITY,
                            'message' => 'The stock should be greater than or equal to {{ compared_value }}.',
                        ]

                    ),
                ])
            )
        );

        if ($result->count() === 2) {
            throw new ValidationFailedException($data, $result);
        }
    }

    /**
     * @param array|mixed $row
     */
    private function validateRow($row): void
    {
        if (!is_array($row) || count($row) !== count($this->header)) {
            throw new ValidatorException('Invalid row format');
        }
    }
}
