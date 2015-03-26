<?php

namespace OroCRM\Bundle\MagentoBundle\ImportExport\Converter;

use Oro\Bundle\IntegrationBundle\ImportExport\DataConverter\AbstractTreeDataConverter;

class NewsletterSubscriberDataConverter extends AbstractTreeDataConverter
{
    /**
     * {@inheritdoc}
     */
    protected function getHeaderConversionRules()
    {
        return [
            'subscriber_id' => 'originId',
            'change_status_at' => 'changeStatusAt',
            'customer_id' => 'customer:originId',
            'subscriber_email' => 'email',
            'subscriber_status' => 'status:id',
            'subscriber_confirm_code' => 'confirmCode'
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function convertToImportFormat(array $importedRecord, $skipNullValues = true)
    {
        if ($this->context && $this->context->hasOption('channel')) {
            $importedRecord['customer:channel:id'] = $this->context->getOption('channel');
        }

        return parent::convertToImportFormat($importedRecord, $skipNullValues);
    }

    /**
     * {@inheritdoc}
     */
    public function convertToExportFormat(array $exportedRecord, $skipNullValues = true)
    {
        $exportedRecord = parent::convertToExportFormat($exportedRecord, $skipNullValues);

        if (!empty($exportedRecord['store'])) {
            $exportedRecord['store_id'] = $exportedRecord['store']['store_id'];
            unset($exportedRecord['store']);
        }

        return $exportedRecord;
    }


    /**
     * {@inheritdoc}
     */
    protected function getBackendHeader()
    {
        return array_values($this->getHeaderConversionRules());
    }
}
