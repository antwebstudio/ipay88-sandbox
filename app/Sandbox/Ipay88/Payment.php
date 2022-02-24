<?php

namespace Ant\Sandbox\Ipay88;

use App\Validator;

class Payment
{
	protected $request;
	protected $sandbox;
	protected $transId;

	public function __construct($sandbox, $request)
	{
		$this->request = $request;
		$this->sandbox = $sandbox;
		$this->transId = 'TEST_' . uniqid();
	}

	protected function rules()
	{
		return [
			'MerchantCode' => ['required', function ($attribute, $value, $fail) {
				if ($value != $this->sandbox->merchantCode) {
					$fail(':attribute is invalid.');
				}
			}],
			'Signature' => ['required', function ($attribute, $value, $fail) {
				if ($value != $this->getSignature()) {
					$fail(':attribute is invalid.');
				}
			}],
			'UserName' => 'required',
			'Amount' => 'required',
			'RefNo' => 'required',
			'Currency' => 'required',
		];
	}

	public function getErrors()
	{
		return Validator::make($this->request->all(), $this->rules())->errors();
	}

	public function getErrorReturnParams()
	{
		$status = 0; // Error

		return [
			'Currency' => $this->request->Currency,
			'PaymentId' => $this->request->PaymentId,
			'RefNo' => $this->request->RefNo,
			'Status' => $status,
			'Amount' => $this->request->Amount,
			'Signature' => $this->generateSignature(),
			'TransId' => $this->transId,
			'ErrDesc' => $this->isSuccessful() ? '' : 'Unknown Error',
			'Remark' => '',
			'ReQueryStatus' => 'Payment Fail',
		];
	}

	public function getCancelReturnParams()
	{
		$data = $this->getReturnParams();
		$data['Status'] = 0;
		$data['ErrDesc'] = 'Customer Cancel Transaction';

		return $data;
	}

	public function getReturnParams()
	{
		$amount = $this->request->Amount;
		$refNo = $this->request->RefNo;
		$currency = $this->request->Currency;
		$paymentId = $this->request->PaymentId;
		$status = $this->isSuccessful() ? 1 : 0;

		return [
			'Currency' => $currency,
			'PaymentId' => $paymentId,
			'RefNo' => $refNo,
			'Status' => $status,
			'Amount' => $amount,
			'Signature' => $this->generateSignature(),
			'TransId' => $this->transId,
			'ErrDesc' => $this->isSuccessful() ? '' : 'Unknown Error',
			'Remark' => '',
		];
	}
	
	public function getExpectedSignature()
	{
		return $this->getSignature();
	}
	
	public function getSignatureString()
	{
		$refNo = $this->request->RefNo;
		$total = $this->request->Amount;
		$currency = $this->request->Currency;

		return $this->sandbox->merchantKey . $this->request->MerchantCode . $refNo . str_replace(['.', ','], '', $total) . $currency;
	}

	protected function isSuccessful()
	{
		return !$this->getErrors()->any();
	}

	// Signature to be post back
	protected function generateSignature()
	{
		$refNo = $this->request->RefNo;
		$total = $this->request->Amount;
		$currency = $this->request->Currency;
		$status = $this->isSuccessful() ? 1 : 0;

		$string = $this->sandbox->merchantKey . $this->sandbox->merchantCode . $refNo . str_replace(['.', ','], '', $total) . $currency . $status;

		return $this->sandbox->createSignatureFromString($string);
	}

	// Signature expected to be received
	protected function getSignature()
	{
		$string = $this->getSignatureString();

		return $this->sandbox->createSignatureFromString($string);
	}
}
