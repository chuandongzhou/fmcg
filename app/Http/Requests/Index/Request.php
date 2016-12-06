<?php

namespace App\Http\Requests\Index;

use Illuminate\Foundation\Http\FormRequest as formRequest;
use Illuminate\Contracts\Validation\Validator;
abstract class Request extends formRequest
{
    public function authorize()
    {
        return true;
    }

    /**
     * 默认validator
     *
     * @param \Illuminate\Contracts\Validation\Factory $factory
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function defaultValidator($factory)
    {
        $rules = $this->container->call([$this, 'rules']);
        return $factory->make($this->all(), $rules, $this->messages(), $this->attributes());
    }

}