<?php namespace Poppy\System\Classes\Form\Field;

use Poppy\System\Classes\Form\Traits\ImageField;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class MultipleImage extends MultipleFile
{
    use ImageField;

    /**
     * @inheritDoc
     */
    protected $view = 'py-system::tpl.form.multiplefile';

    /**
     *  Validation rules.
     *
     * @var string
     */
    protected $rules = 'image';

    /**
     * Prepare for each file.
     *
     * @param UploadedFile $image
     *
     * @return mixed|string
     */
    protected function prepareForeach(UploadedFile $image = null)
    {
        $this->name = $this->getStoreName($image);

        $this->callInterventionMethods($image->getRealPath());

        return tap($this->upload($image), function () {
            $this->name = null;
        });
    }
}
