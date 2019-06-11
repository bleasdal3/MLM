<?php

namespace App\Http\Controllers;

class RightmoveEmailParser
{
    private $html;
    private $htmllessLineArray;

    public function __construct(string $emailHtml)
    {
        $this->html = $emailHtml;
        $this->htmllessLineArray = $this->makeHtmllessLineArray($emailHtml);
    }
    
    public function name()
    {
        return $this->findValueWithTitle('Name:');
    }

    public function email()
    {
        return $this->findValueWithTitle('Email:');
    }

    public function telephone()
    {
        return $this->findValueWithTitle('Phone:');
    }

    public function country()
    {
        return $this->findValueWithTitle('Country:');
    }

    public function reasonForBuying()
    {
        return $this->findValueWithTitle('Reason For Buying:', 2);
    }

    public function comments()
    {
        return $this->findValueWithTitle('Comments', 3);
    }

    public function reference()
    {
        return $this->findValueWithTitle('Reference:');
    }

    public function propertyLink()
    {
        $splitOnATag = explode('<a href="', $this->html);
        foreach ($splitOnATag as $aTagStart) {
            $splitOnATagClose = explode('" target="_blank">', $aTagStart);
            if (strpos($splitOnATagClose[0], 'https://www.rightmove.co.uk/overseas-property/') !== false) {
                return $splitOnATagClose[0];
            }
        }
    }

    private function findValueWithTitle(string $title, int $linesAfterTitle = 1)
    {
        foreach ($this->htmllessLineArray as $index => $line) {
            if ($line == $title) {
                $indexOfValue = $index + $linesAfterTitle;
                while($this->htmllessLineArray[$indexOfValue] === ''){
                    $indexOfValue++;
                }
                return $this->htmllessLineArray[$indexOfValue];
            }
        }
    }

    private function makeHtmllessLineArray($emailHtml)
    {
        $htmlWithoutTags = strip_tags($emailHtml);
        $arrayOfLines = explode("\r\n", $htmlWithoutTags);
        $linesWithoutSpaces = [];
        foreach ($arrayOfLines as $line) {
            $linesWithoutSpaces[] = trim($line);
        }
        return $linesWithoutSpaces;
    }
}
