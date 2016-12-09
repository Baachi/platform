<?php

namespace Oro\Bundle\ImportExportBundle\Async;

class Topics
{
    const IMPORT_CLI = 'oro.importexport.import_cli';
    const IMPORT_CLI_VALIDATION = 'oro.importexport.import_cli_validation';
    const IMPORT_HTTP = 'oro.importexport.import_http';
    const IMPORT_HTTP_PREPARING = 'oro.importexport.import_http_preparing';
    const IMPORT_HTTP_VALIDATION = 'oro.importexport.import_http_validation';
    const IMPORT_HTTP_VALIDATION_PREPARING = 'oro.importexport.import_http_validation_preparing';
    const EXPORT = 'oro.importexport.export';
}
