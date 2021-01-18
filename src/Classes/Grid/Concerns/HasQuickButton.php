<?php namespace Poppy\System\Classes\Grid\Concerns;

use Poppy\System\Classes\Grid\Tools\BaseButton;

trait HasQuickButton
{
    protected $quickButtons = [];

    /**
     * Get create url.
     *
     * @param BaseButton[]|BaseButton $buttons
     */
    public function appendQuickButton($buttons): array
    {

        if (is_array($buttons) && count($buttons)) {
            foreach ($buttons as $button) {
                $this->appendQuickButton($button);
            }
        }
        else {
            $this->quickButtons[] = $buttons;
        }
        return $this->quickButtons;
    }

    /**
     * Render create button for grid.
     *
     * @return string
     */
    public function renderQuickButton(): string
    {
        $append = '';
        foreach ($this->quickButtons as $quickButton) {
            $append .= $quickButton->render();
        }
        return $append;
    }
}
