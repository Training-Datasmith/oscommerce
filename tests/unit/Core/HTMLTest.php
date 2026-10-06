<?php

declare(strict_types=1);

namespace Tests\Unit\Core;

use osCommerce\OM\Core\DateTime;
use osCommerce\OM\Core\HTML;
use osCommerce\OM\Core\Registry;
use PHPUnit\Framework\TestCase;
use Tests\Support\ShopHtmlFixture;

class HTMLTest extends TestCase
{
    protected function setUp(): void
    {
        ShopHtmlFixture::boot();
    }

    public function testOutput(): void
    {
        $this->assertSame('test&quot;string', HTML::output(' test"string '));
    }

    public function testOutputWithAmpersand(): void
    {
        $this->assertSame(
            'test&quot;string &amp;',
            HTML::output(' test"string & ', ['&' => '&amp;', '"' => '&quot;'])
        );
    }

    public function testOutputProtected(): void
    {
        $this->assertSame(
            '&lt;a href=&quot;test&quot;&gt;test&amp;string&lt;/a&gt;',
            HTML::outputProtected(' <a href="test">test&string</a> ')
        );
    }

    public function testSanitize(): void
    {
        $this->assertSame('test _test_', HTML::sanitize(' test      <test> '));
    }

    public function testLink(): void
    {
        $this->assertSame(
            '<a href="http://www.oscommerce.com" target="_blank">osCommerce</a>',
            HTML::link('http://www.oscommerce.com', 'osCommerce', 'target="_blank"')
        );
    }

    public function testImageWithWidthAndHeight(): void
    {
        $this->assertSame(
            '<img src="http://www.oscommerce.com/images/oscommerce.gif" border="0" alt="osCommerce" title="osCommerce" width="211" height="60" id="logo" />',
            HTML::image('http://www.oscommerce.com/images/oscommerce.gif', 'osCommerce', 211, 60, 'id="logo"')
        );
    }

    public function testImageWithoutWidthAndHeight(): void
    {
        $this->assertSame(
            '<img src="http://www.oscommerce.com/images/oscommerce.gif" border="0" alt="osCommerce" title="osCommerce" id="logo" />',
            HTML::image('http://www.oscommerce.com/images/oscommerce.gif', 'osCommerce', '', '', 'id="logo"')
        );
    }

    public function testIcon(): void
    {
        $this->assertSame(
            '<img src="public/sites/Shop/templates/oscom/images/icons/16x16/info.png" border="0" alt="Info" title="Info" id="iconInfo" />',
            HTML::icon('info.png', 'Info', '16x16', 'id="iconInfo"')
        );
    }

    public function testIconRaw(): void
    {
        $this->assertSame(
            'public/sites/Shop/templates/oscom/images/icons/16x16/info.png',
            HTML::iconRaw('info.png', '16x16')
        );
    }

    public function testSubmitImage(): void
    {
        $this->assertSame(
            '<input type="image" src="public/sites/Shop/templates/oscom/images/icons/16x16/edit.png" title="Edit" id="editSubmitImage" />',
            HTML::submitImage(HTML::iconRaw('edit.png'), 'Edit', 'id="editSubmitImage"')
        );
    }

    public function testButtonSubmit(): void
    {
        $this->assertSame(
            '<button id="button1" type="submit">Submit</button><script type="text/javascript">$("#button1").button({icons:{primary:"ui-icon-tick"}});</script>',
            HTML::button(['title' => 'Submit', 'icon' => 'tick'])
        );
    }

    public function testButtonReset(): void
    {
        $this->assertSame(
            '<button id="button2" type="submit">Reset</button><script type="text/javascript">$("#button2").button();</script>',
            HTML::button(['title' => 'Reset'])
        );
    }

    public function testButtonButton(): void
    {
        $this->assertSame(
            '<button id="button3" type="button" onclick="window.open(\'http://www.oscommerce.com\');">osCommerce</button><script type="text/javascript">$("#button3").button({icons:{secondary:"ui-icon-tick"}}).addClass("ui-priority-secondary");</script>',
            HTML::button(['href' => 'http://www.oscommerce.com', 'newwindow' => true, 'title' => 'osCommerce', 'icon' => 'tick', 'iconpos' => 'right', 'priority' => 'secondary'])
        );
    }

    public function testInputField(): void
    {
        $this->assertSame(
            '<input type="text" name="site" value="osCommerce" id="ifName" />',
            HTML::inputField('site', 'osCommerce', 'id="ifName"')
        );
    }

    public function testPasswordField(): void
    {
        $this->assertSame(
            '<input type="password" name="password" id="pfPassword" />',
            HTML::passwordField('password', 'id="pfPassword"')
        );
    }

    public function testTextareaField(): void
    {
        $this->assertSame(
            '<textarea name="description" cols="6" rows="65" id="taDescription">Description</textarea>',
            HTML::textareaField('description', 'Description', 6, 65, 'id="taDescription"')
        );
    }

    public function testSelectMenu(): void
    {
        $list_array = [
            ['id' => 'one', 'text' => 'First'],
            ['id' => 'two', 'text' => 'Second'],
            ['id' => 'three', 'text' => 'Third'],
        ];

        $this->assertSame(
            '<select name="list" id="sList"><option value="one">First</option><option value="two" selected="selected">Second</option><option value="three">Third</option></select>',
            HTML::selectMenu('list', $list_array, 'two', 'id="sList"')
        );
    }

    public function testCheckboxField(): void
    {
        $list_array = [
            ['id' => 'one', 'text' => 'First'],
            ['id' => 'two', 'text' => 'Second'],
            ['id' => 'three', 'text' => 'Third'],
        ];

        $this->assertSame(
            '<input type="checkbox" name="selection" id="selection_1" value="one" /><label for="selection_1" class="fieldLabel">First</label>&nbsp;&nbsp;<input type="checkbox" name="selection" id="selection_2" value="two" checked="checked" /><label for="selection_2" class="fieldLabel">Second</label>&nbsp;&nbsp;<input type="checkbox" name="selection" id="selection_3" value="three" /><label for="selection_3" class="fieldLabel">Third</label>',
            HTML::checkboxField('selection', $list_array, 'two')
        );
    }

    public function testRadioField(): void
    {
        $list_array = [
            ['id' => 'one', 'text' => 'First'],
            ['id' => 'two', 'text' => 'Second'],
            ['id' => 'three', 'text' => 'Third'],
        ];

        $this->assertSame(
            '<input type="radio" name="selection" id="selection_1" value="one" checked="checked" /><label for="selection_1" class="fieldLabel">First</label>&nbsp;&nbsp;<input type="radio" name="selection" id="selection_2" value="two" /><label for="selection_2" class="fieldLabel">Second</label>&nbsp;&nbsp;<input type="radio" name="selection" id="selection_3" value="three" /><label for="selection_3" class="fieldLabel">Third</label>',
            HTML::radioField('selection', $list_array, 'one')
        );
    }

    public function testHiddenField(): void
    {
        $this->assertSame(
            '<input type="hidden" name="action" value="confirm" id="hfAction" />',
            HTML::hiddenField('action', 'confirm', 'id="hfAction"')
        );
    }

    public function testHiddenSessionIDField(): void
    {
        $this->assertSame('', HTML::hiddenSessionIDField());
    }

    public function testLabel(): void
    {
        $this->assertSame(
            '<label for="firstname">First Name</label>',
            HTML::label('First Name', 'firstname')
        );
    }

    public function testDateSelectMenu(): void
    {
        $currentYear = (int) date('Y');
        $yearRangeStart = $currentYear - 2011;
        $yearRangeEnd = 2012 - $currentYear;

        $html = HTML::dateSelectMenu(
            'date',
            ['year' => 2011, 'month' => 1, 'date' => 1],
            false,
            true,
            true,
            $yearRangeStart,
            $yearRangeEnd
        );

        $this->assertStringContainsString('name="date_days"', $html);
        $this->assertStringContainsString('value="1" selected="selected"', $html);
        $this->assertStringContainsString('January', $html);
        $this->assertStringContainsString('value="2011" selected="selected"', $html);
        $this->assertStringContainsString('value="2012"', $html);
        $this->assertStringNotContainsString('value="2010"', $html);
    }

    public function testTimeZoneSelectMenu(): void
    {
        $result = [];

        foreach (DateTime::getTimeZones() as $zone => $zones_array) {
            foreach ($zones_array as $key => $value) {
                $result[] = [
                    'id' => $key,
                    'text' => $value,
                    'group' => $zone,
                ];
            }
        }

        $this->assertSame(
            HTML::selectMenu('timezone', $result, 'Europe/Berlin'),
            HTML::timeZoneSelectMenu('timezone', 'Europe/Berlin')
        );
    }
}
