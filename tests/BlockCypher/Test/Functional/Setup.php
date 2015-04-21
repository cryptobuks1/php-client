<?php

namespace BlockCypher\Test\Functional;

use BlockCypher\Auth\SimpleTokenCredential;
use BlockCypher\Core\BlockCypherCredentialManager;
use BlockCypher\Rest\ApiContext;

class Setup
{
    public static $mode = 'mock';

    public static function SetUpForFunctionalTests(\PHPUnit_Framework_TestCase &$test)
    {
        $configs = array(
            'mode' => 'sandbox',
            'http.ConnectionTimeOut' => 30,
            'log.LogEnabled' => true,
            'log.FileName' => '../BlockCypher.log',
            'log.LogLevel' => 'FINE',
            'validation.level' => 'log'
        );

        /*
        // OAuthTokenCredential is still not supported
        $test->apiContext = new ApiContext(
            new OAuthTokenCredential('AYSq3RDGsmBLJE-otTkBtM-jBRd1TCQwFf9RGfwddNXWz0uFU9ztymylOhRS', 'EGnHDxD_qRPdaLdZz8iCr8N7_MzF-YHPTkjs6NKYQvQSBngp4PTTVWkPZRbL')
        );
        */

        // Replace these values by entering your own token by visiting https://accounts.blockcypher.com/
        /** @noinspection SpellCheckingInspection */
        $token = 'c0afcccdde5081d6429de37d16166ead';
        /** @noinspection PhpUndefinedFieldInspection */
        $test->apiContext = new ApiContext(new SimpleTokenCredential($token));

        /** @noinspection PhpUndefinedFieldInspection */
        $test->apiContext->setConfig($configs);

        //BlockCypherConfigManager::getInstance()->addConfigFromIni(__DIR__. '/../../../sdk_config.ini');
        //BlockCypherConfigManager::getInstance()->addConfigs($configs);
        BlockCypherCredentialManager::getInstance()->setCredentialObject(BlockCypherCredentialManager::getInstance()->getCredentialObject('acct1'));

        self::$mode = getenv('REST_MODE') ? getenv('REST_MODE') : 'mock';
        if (self::$mode != 'sandbox') {

            // Mock BlockCypherRest Caller if mode set to mock
            $test->mockBlockCypherRestCall = $test->getMockBuilder('\BlockCypher\Transport\BlockCypherRestCall')
                ->disableOriginalConstructor()
                ->getMock();

            /** @noinspection PhpUndefinedFieldInspection */
            $test->mockBlockCypherRestCall->expects($test->any())
                ->method('execute')
                ->will($test->returnValue(
                    $test->response
                ));
        }
    }
}