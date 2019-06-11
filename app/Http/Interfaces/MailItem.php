<?php

namespace App\Http\Interfaces;

interface MailItem
{

    /**
     * Get the name from the email body
     *
     * @return string
     */
    public function name();

    /**
     * Get the email address from the email body
     *
     * @return string
     */
    public function email();

    /**
     * Get the phone from the email body
     *
     * @return string
     */
    public function telephone();

    /**
     * Get the property reference from the email body
     *
     * @return string
     */
    public function propertyReference();

    /**
     * Get the country from the email body
     *
     * @return string
     */
    public function country();

    /**
     * Get the reason for buying from the email body
     *
     * @return string
     */
    public function reasonForBuying();

    /**
     * Get the comments from the email body
     *
     * @return string
     */
    public function comments();

    /**
     * Get the comments from the email body
     *
     * @return string
     */
    public function propertyLink();

}
