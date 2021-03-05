<?php

namespace Ant\Sandbox;

class Ipay88Sandbox
{
    public $merchantCode;
    public $merchantKey;

    protected $allowVerb = ['POST'];
    protected $processor;
    protected $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function config($config)
    {
        foreach ($config as $key => $value) {
            $this->{$key} = $value;
        }
    }

    public function errors()
    {
        return $this->processor->getErrors();
    }

    public function successResponse()
    {
        $response = new Response;
        $response->setUrl($this->request->ResponseURL);
        $response->setParams($this->processor->getReturnParams());

        return $response;
    }

    public function errorResponse()
    {
        $response = new Response;
        $response->setUrl($this->request->ResponseURL);
        $response->setParams($this->processor->getErrorReturnParams());

        return $response;
    }

    public function cancelResponse()
    {
        $response = new Response;
        $response->setUrl($this->request->ResponseURL);
        $response->setParams($this->processor->getCancelReturnParams());

        return $response;
    }

    public function createSignatureFromString($fullStringToHash)
    {
        return base64_encode(self::hex2bin(sha1($fullStringToHash)));
    }

    public function process()
    {
        if ($this->canProcess()) {
            $this->processor = new Ipay88\Payment($this, $this->request);
        } else {
            throw new \Exception('Only method: ' . implode(', ', $this->allowVerb) . ' is allowed for this page. ');
        }
    }

    protected function canProcess()
    {
        return in_array(strtoupper($this->getRequestMethod()), $this->allowVerb);
    }

    protected function getRequestMethod()
    {
        return $this->request->method();
    }

    protected static function hex2bin($hexSource)
    {
        $bin = '';
        for ($i = 0; $i < strlen($hexSource); $i = $i + 2) {
            $bin .= chr(hexdec(substr($hexSource, $i, 2)));
        }
        return $bin;
    }
}
