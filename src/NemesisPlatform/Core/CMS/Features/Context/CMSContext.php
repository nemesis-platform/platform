<?php

namespace NemesisPlatform\Core\CMS\Features\Context;

use NemesisPlatform\Components\Test\Behat\Context\RawSymfonyContext;

/**
 * Features context.
 */
class CMSContext extends RawSymfonyContext
{


    /**
     * @Given /^I wait for (\d+) milliseconds$/
     * @param $waitTime
     *
     * @return bool
     */
    public function iWaitForMilliseconds($waitTime)
    {
        return $this->getSession()->wait($waitTime, 'false');
    }

    /**
     * @Given /^I wait for "([^"]*)" autocomplete to finish$/
     * @param $element
     */
    public function iWaitForAutocompleteToFinish($element)
    {
        $field = $this->getSession()->getPage()->findField($element);
        $this->getSession()->wait(
            3000,
            "($('#".$field->getAttribute('id')."').data('uiAutocomplete').menu.element.children().length > 0)"
        );
    }

    /**
     * @Given /^I click on the first "([^"]*)" autocomplete element$/
     * @param $element
     */
    public function iClickOnTheFirstAutocompleteElement($element)
    {
        $field = $this->getSession()->getPage()->findField($element);
        $this->getSession()->executeScript(
            "$('#".$field->getAttribute('id')."').data('uiAutocomplete').menu.element.children().first().click()"
        );
    }
}
