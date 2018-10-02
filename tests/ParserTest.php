<?php

namespace ACFBentveld\Parser\Tests;

use ACFBentveld\Parser\InvalidTagsException;
use ACFBentveld\Parser\Parser;
use PHPUnit\Framework\TestCase;

class ParserTest extends TestCase
{
    /** @test */
    public function it_allows_null()
    {
        $values = [
            'first_name' => 'Foo',
            'last_name'  => 'bar'
        ];

        $input = null;

        $expected = null;

        $result = Parser::text($input)->values($values)->parse();

        $this->assertEquals($expected, $result);
    }


    /** @test */
    public function it_sets_default_tags()
    {
        $values = [
            'name' => 'Foobar'
        ];

        $input = "Name is: %name% [name]";

        $expected = "Name is: %name% Foobar";

        $result = Parser::text($input)->values($values)->tags(['', ''])->parse();

        $this->assertEquals($expected, $result);
    }


    /** @test */
    public function it_maps_aliases()
    {
        $values = [
            'name' => 'Foobar'
        ];

        $aliases = [
            'naam' => 'name'
        ];

        $expected = [
            'naam' => 'Foobar'
        ];

        $mapped = Parser::text("Name is: [naam]")->values($values)->aliases($aliases)->mapAliases();

        $this->assertEquals($expected, $mapped);
    }


    /** @test */
    public function it_parses()
    {
        $values = [
            'first_name' => 'Foo',
            'last_name'  => 'bar'
        ];

        $aliases = [
            'voornaam'   => 'first_name',
            'achternaam' => 'last_name'
        ];

        $input = "[first_name] [last_name] is the same as [voornaam] [achternaam]!";

        $expected = "Foo bar is the same as Foo bar!";

        $result = Parser::text($input)->values($values)->aliases($aliases)->parse();

        $this->assertEquals($expected, $result);
    }


    /** @test */
    public function it_ignores_keys()
    {
        $values = [
            'name'  => 'Foobar',
            'token' => '123456'
        ];

        $aliases = [
            'token_alias' => 'token'
        ];

        $input = "[token] and [token_alias] should not be parsed but [name] should";

        $expected = "[token] and [token_alias] should not be parsed but Foobar should";

        $result = Parser::text($input)->values($values)->aliases($aliases)->exclude(['token'])->parse();

        $this->assertEquals($expected, $result);
    }


    /** @test */
    public function it_works_with_tags()
    {
        $values = [
            'name' => 'Foobar'
        ];

        $input = "Name is: %name%";

        $expected = "Name is: Foobar";

        $result = Parser::text($input)->values($values)->tags(['%', '%'])->parse();

        $this->assertEquals($expected, $result);
    }


    /** @test */
    public function it_works_with_dual_tags()
    {
        $values = [
            'name' => 'Foobar'
        ];

        $input = "Name is: %name}";

        $expected = "Name is: Foobar";

        $result = Parser::text($input)->values($values)->tags(['%', '}'])->parse();

        $this->assertEquals($expected, $result);
    }


    /** @test */
    public function it_parses_closure()
    {
        $age = rand(1, 100);
        $date = "2018-01-01";

        $values = [
            'name'      => 'Foobar',
            'age'       => function () use ($age) {
                return $age;
            },
            'birthdate' => function () use ($date) {
                return (new \DateTime($date))->format("d-m-Y");
            }
        ];

        $input = "[name] is [age] years old and born on [birthdate]";

        $expected = "Foobar is {$age} years old and born on 01-01-2018";

        $result = Parser::text($input)->values($values)->parse();

        $this->assertEquals($expected, $result);
    }


    /** @test */
    public function it_ignores_invalid_types()
    {
        $values = [
            'name'     => null,
            'age'      => [1, 2, 3],
            'active'   => true,
            'language' => 'en',
            'date'     => function () {
                return (new \DateTime());
            }
        ];

        $input = "[name] [age] [active] [language] [date]";

        $expected = "[name] [age] 1 en [date]";

        $result = Parser::text($input)->values($values)->parse();

        $this->assertEquals($expected, $result);
    }


    /** @test */
    public function it_handles_invalid_tags()
    {
        $values = [
            'name' => 'Foobar'
        ];

        $input = "[name]";

        $expected = "Foobar";

        $this->expectException(InvalidTagsException::class);

        $result = Parser::text($input)->values($values)->tags(['['])->parse();

        $this->assertEquals($expected, $result);
    }


    /** @test */
    public function it_doesnt_loop()
    {
        $values = [
            'name' => '[name]'
        ];

        $input = "[name]";

        $expected = "[name]";

        $result = Parser::text($input)->values($values)->parse();

        $this->assertEquals($expected, $result);
    }


    /** @test */
    public function it_parses_nested_array_values()
    {
        $values = [
            'user' => [
                'name'  => [
                    'first_name' => 'Foo',
                    'last_name'  => 'Bar'
                ],
                'email' => 'example@example.com'
            ]
        ];

        $input = "[user.name.first_name][user.name.last_name] - [user.email]";

        $expected = "FooBar - example@example.com";

        $result = Parser::text($input)->values($values)->parse();

        $this->assertEquals($expected, $result);
    }
}