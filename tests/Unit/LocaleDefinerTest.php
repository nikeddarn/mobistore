<?php

namespace Tests\Unit;

use App\Localization\LocaleDefiner;
use Illuminate\Http\Request;
use Mockery;
use Tests\TestCase;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Foundation\Testing\DatabaseTransactions;

class LocaleDefinerTest extends TestCase
{

    private $request;

    private $session;


    public function setUp()
    {
        parent::setUp();

        $this->request = Mockery::mock(Request::class);
        $this->session = Mockery::mock(\Illuminate\Contracts\Session\Session::class);
    }

    public function localeDataProvider(){
        return [
            [null, null, null, 'en'],
            ['ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4', null, null, 'ru'],
            ['de-DE,de;q=1.0,ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4', null, null, 'ru'],
            ['de-DE,de;q=1.0', null, null, 'en'],
            ['de-DE,de;q=1.0', 'ru', null, 'ru'],
            ['de-DE,de;q=1.0', 'ru', 'en', 'ru'],
            ['de-DE,de;q=1.0', 'ua', 'en', 'ua'],
            ['de-DE,de;q=1.0', null, 'en', 'en'],
            ['de-DE,de;q=1.0', null, 'ru', 'ru'],
            ['de-DE,de;q=1.0', null, 'es', 'en'],
            ['de-DE,de;q=1.0', 'es', null, 'en'],
            ['ru-RU,ru;q=0.8,en-US;q=0.6,en;q=0.4', 'en', null, 'en'],
        ];
    }

    /**
     * @param string $header
     * @param string $session
     * @param string $cookie
     * @param string $expected
     *
     * @dataProvider localeDataProvider
     */
    public function testDefineLocale($header, $session, $cookie, string $expected)
    {
        $this->createStub($header, $session, $cookie);

        $definer = new LocaleDefiner();

        $locale = $definer->getLocale($this->request);

        $this->assertEquals($expected, $locale);
    }

    public function createStub($header, $session, $cookie){
        $this->request->shouldReceive('header')->once()->andReturn($header);

        $this->session->shouldReceive('has')->with('locale')->andReturn($session);
        $this->session->shouldReceive('get')->with('locale')->andReturn($session);

        $this->request->shouldReceive('session')->andReturn($this->session);
        $this->request->shouldReceive('cookie')->with('locale')->andReturn($cookie);
    }

    public function tearDown()
    {
        Mockery::close();
    }
}
