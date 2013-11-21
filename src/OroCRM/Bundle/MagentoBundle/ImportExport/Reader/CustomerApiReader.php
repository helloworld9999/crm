<?php

namespace OroCRM\Bundle\MagentoBundle\ImportExport\Reader;

use Oro\Bundle\BatchBundle\Step\StepExecutionAwareInterface;
use Oro\Bundle\ImportExportBundle\Context\ContextInterface;
use Oro\Bundle\ImportExportBundle\Context\ContextRegistry;
use Oro\Bundle\ImportExportBundle\Reader\AbstractReader;
use Oro\Bundle\ImportExportBundle\Reader\ReaderInterface;
use Oro\Bundle\IntegrationBundle\Entity\Connector as ConnectorEntity;
use Oro\Bundle\IntegrationBundle\Provider\ConnectorInterface;

use OroCRM\Bundle\MagentoBundle\Provider\CustomerConnector;

class CustomerApiReader extends AbstractReader implements ReaderInterface, StepExecutionAwareInterface
{
    /** @var CustomerConnector */
    protected $customerConnector;

    /** @var \Closure */
    protected $loggerClosure;

    /**
     * @param ContextRegistry $contextRegistry
     * @param ConnectorInterface $customerConnector
     */
    public function __construct(ContextRegistry $contextRegistry, ConnectorInterface $customerConnector)
    {
        parent::__construct($contextRegistry);

        $this->customerConnector = $customerConnector;
    }

    /**
     * {@inheritdoc}
     */
    protected function initializeFromContext(ContextInterface $context)
    {
        $channelName = $context->getOption('channelName');
        $this->loggerClosure = $context->getOption('logger');

        /** @var ConnectorEntity $connector */
        $connectorEntity = $context->getOption('connector');
        $this->customerConnector->setConnectorEntity($connectorEntity);
    }

    /**
     * {@inheritdoc}
     */
    public function read()
    {
        // read peace of data, skipping empty
        do {
            $data = $this->customerConnector->read();
        } while ($data === false);

        if (is_null($data)) {
            return null; // no data anymore
        }

        $context = $this->getContext();
        $context->incrementReadCount();
        $context->incrementReadOffset();

        // customer connector knows how to advance
        // batch counter/boundaries to the next ones

        if (is_callable($this->loggerClosure)) {
            $this->loggerClosure("Reading item...");
        }

        return $data;
    }
}
