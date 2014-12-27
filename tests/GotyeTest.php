<?php

require_once 'PHPUnit/Autoload.php';
require_once dirname(dirname(__FILE__)) . '/src/Gotye.php';

class GotyeTest extends PHPUnit_Framework_TestCase
{
  private $email = '';
  private $devpwd = '';
  public $appkey = '';

  public function testConstruct() {
    $gotye = new Yan\Gotye($this->email, $this->devpwd, $this->appkey);
    $this->assertInstanceOf('Yan\Gotye', $gotye);
    $this->assertObjectHasAttribute('appkey', $gotye);
    $this->assertEquals(array('email'=> $this->email, 'devpwd' => $this->devpwd, 'appkey' => $this->appkey), $gotye->getBaseData());
  }

  /**
   * @depends testConstruct
   */
  public function testAddUser() {
    $gotye = new Yan\Gotye($this->email, $this->devpwd, $this->appkey);
    $this->assertInstanceOf('Exception', $gotye->addUser(1, 1, 1));
  }
}
