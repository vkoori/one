<?php 

namespace One\Validation;

use Rakit\Validation\Validator;

class FormRequest
{
	/**
	 * @var \Rakit\Validation\Validator
	 */
	private $validator;

	/**
	 * @var \Rakit\Validation\Validation
	 */
	private $validation;

	public function __construct()
	{
		$this
		->setValidator()
		->makeValidate()
		->setAttributes()
		->checkFailsValidator();
	}

	public function validationData(): array
	{
		return request()->all();
	}

	public function customRules(): array
	{
		return [];
	}

	public function rules(): array
	{
		return [];
	}

	public function messages(): array
	{
		return [];
	}

	public function attributes(): array
	{
		return [];
	}

	public function failedValidation(\Rakit\Validation\Validation $validation)
	{
		throw new ValidationException(
			errors: $this->getErrors(exections: $validation->errors()->toArray()),
			code: 422
		);

	}

	public function validated(): array
	{
		$allReq = $this->validationData();
		$validated = $this->validation->getValidData();

		return array_filter(
			array: $validated, 
			callback: function($key) use ($allReq): bool {
				return isset($allReq[$key]);
			}, 
			mode: ARRAY_FILTER_USE_KEY
		);
	}

	private function setValidator(): self
	{

		$this->validator = new Validator(
			messages: __('validation')
		);

		return $this;
	}

	private function makeValidate(): self
	{
		$customRules = $this->customRules();
		foreach ($customRules as $key => $value) {
			$this->validator->addValidator($key, new $value);
		}

		$this->validation = $this->validator->make(
			inputs: $this->validationData(),
			rules: $this->rules(),
			messages: $this->messages()
		);

		return $this;
	}

	private function setAttributes(): self
	{
		$this->validation->setAliases(
			aliases: $this->attributes()
		);

		return $this;
	}

	private function checkFailsValidator(): void
	{
		$this->validation->validate();
		if ($this->validation->fails()) {
			$this->failedValidation(validation: $this->validation);
		}
	}

	private function getErrors(array $exections): array
	{
		$errors = [];
		foreach ($exections as $key => $e) {
			$errors[$key] = array_values($e);
		}

		return $errors;
	}

}