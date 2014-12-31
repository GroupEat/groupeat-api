<?php namespace Groupeat\Support\Services\Traits;

use Illuminate\Support\MessageBag as Bag;
use Illuminate\Validation\Validator;
use Validator as ValidatorFactory;

trait PerformValidation {

    /**
     * @var Bag
     */
    private $errorBag;


    /**
     * @return Bag
     */
    public function errors()
    {
        if (is_null($this->errorBag))
        {
            $this->errorBag = new Bag;
        }

        return $this->errorBag;
    }

    protected function checkRules(array $fields, array $rules)
    {
        $validator = $this->makeValidator($fields, $rules);

        return $this->checkValidator($validator);
    }

    /**
     * @param array $fields
     * @param array $rules
     *
     * @return Validator
     */
    protected function makeValidator(array $fields, array $rules)
    {
        return ValidatorFactory::make($fields, $rules);
    }

    /**
     * @param Validator $validator
     *
     * @return bool
     */
    protected function checkValidator(Validator $validator)
    {
        $isValid = $validator->passes();

        $this->setErrors($validator->messages());

        return $isValid;
    }

    /**
     * @param Bag $errors
     *
     * @return $this
     */
    protected function setErrors(Bag $errors)
    {
        $this->errorBag = $errors;

        return $this;
    }

}
