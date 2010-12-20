<?php

require_once 'PHPUnit/Framework/TestCase.php';

class ProtobufTest extends PHPUnit_Framework_TestCase
{
    
    public static function setUpBeforeClass() {
        file_put_contents('addressbook.proto', '
        
// import the php_options so we can define custom options for the compiler.  
import "php_options.proto";

package unit_test;

option java_package = "com.example.unit_test";
option java_outer_classname = "UnitTestAddressBookProtos";

option (php_package) = "Unit_Test";
option (php_multiple_files) = true;

message Person {
  required string name = 1;
  required int32 id = 2;        // Unique ID number for this person.
  optional string email = 3;

  enum PhoneType {
    MOBILE = 0;
    HOME = 1;
    WORK = 2;
  }

	message Unused {
	  required string test = 1;
	}

  message PhoneNumber {
    required string number = 1;
    optional PhoneType type = 2 [default = HOME];
  }

  repeated PhoneNumber phone = 4;

 optional sint32 negative = 6;
}

// Our address book file is just one of these.
message AddressBook {
  repeated Person person = 1;
}

        ');
    }
    
    
    public static function tearDownAfterClass() {
	    	//return;
        $return = ''; $out = array();
        exec('cd ' . escapeshellarg(dirname( __FILE__ )) .';', $out, $return);
        exec('make clean;', $return);
        unlink('AddressbookProto.php');
        unlink('addressbook.proto');
    }
    
    public function testMake() {
        //return;
        $return = ''; $out = array();
        exec('cd ' . escapeshellarg(dirname( __FILE__ )) .';', $out, $return);
        exec('make clean;', $out, $return);
        exec('make;', $out, $return);
        
        $out = join("\n",$out);
        
        if (stristr($out,' warning:') || stristr($out,' error:')) {
            $this->assertTrue(FALSE, "\n$out\n");
        }
        
    }

    public function testBuild() {
    //return;
        $return = ''; $out = array();
        exec('cd ' . escapeshellarg(dirname( __FILE__ )) .';', $out, $return);
        exec('protoc -I. -I/usr/include -I/usr/include -I/usr/local/include --php_out . --plugin=protoc-gen-php=./protoc-gen-php addressbook.proto;', $out, $return);
    }
    
    
    public function testNegative() {
    
        require_once('AddressbookProto.php');
        
        # Generate a new message 
        $book = new Unit_Test_AddressBook();

				$id = 1;
				
				$p = new Unit_Test_Person();
				$p->setName('Person');
				$p->setId($id++);
				$p->setNegative(43480333);
				$book->addPerson($p);
				
				$p = new Unit_Test_Person();
				$p->setName('Person');
				$p->setId($id++);
				$p->setNegative(-80544722);
				$book->addPerson($p);
				$p = new Unit_Test_Person();
				
				/*
				
				$p = new Unit_Test_Person();
				$p->setName('Person');
				$p->setId($id++);
				$p->setNegative(123000000);
				$book->addPerson($p);
				
				$p = new Unit_Test_Person();
				$p->setName('Person');
				$p->setId($id++);
				$p->setNegative(100);
				$book->addPerson($p);
				
				$p = new Unit_Test_Person();
				$p->setName('Person');
				$p->setId($id++);
				$p->setNegative(1);
				$book->addPerson($p);
*/

				$pbData = $book->__toString();
				$this->assertNotEmpty((string)$book);
				$this->assertEquals((string)$book,$pbData);
        
         # Serialize the data to a .pb format.
        $tmp = dirname(__FILE__) . '/addressbook.pb';
				$outfp = fopen($tmp, 'wb');
        $book->write($outfp);
        fclose($outfp);

				// check that the string output is correct
				//var_dump($book->getPbString(),file_get_contents('python/addressbook.pb'));

        // Read it into a new addressbook
        $infp = fopen($tmp, 'rb');
        $newBook = new Unit_Test_AddressBook($infp);
				fclose($infp);		
    		
    }
    
    public function testUsage() {
    		return;
        require_once('AddressbookProto.php');
        
        # Generate a new message 
        $book = new Unit_Test_AddressBook();

        $Person1 = new Unit_Test_Person();
        $Person1->setName('Person1');
        $Person1->setId(1);
				$Person1->setNegative(-100);

        $book->addPerson($Person1);

        $Person2 = new Unit_Test_Person();
        $Person2->name = 'Person2';
        $Person2->setId(2);
        $book->addPerson($Person2);

        $Person3 = new Unit_Test_Person();
        $Person3->setName('Person3');
        $Person3->setId(3);
        $book->addPerson($Person3);

        $jeffrey = new Unit_Test_Person();
        $jeffrey->setName('Jeffrey');
        $jeffrey->setEmail('jsambells@wecreate.com');
        $jeffrey->setId(4);
        $book->addPerson($jeffrey);
        
        $phone = new Unit_Test_Person_PhoneNumber();
        $phone->setNumber('5195551234');
        $phone->setType(Unit_Test_Person_PhoneType::MOBILE);
        $jeffrey->addPhone($phone);
        
        $phone = new Unit_Test_Person_PhoneNumber();
        $phone->number = '5195552345';
        $phone->type = Unit_Test_Person_PhoneType::WORK;
        $jeffrey->addPhone($phone);
        
        # Run some tests to ensure things were set properly.
        $this->assertEquals(4,$book->getPersonCount());
        $person = $book->getPerson(0);
				$this->assertEquals("Person1",$person->getName());
				$this->assertEquals(-100,$person->negative);
				$this->assertEquals(-100,$person->getNegative());
				$this->assertEquals("Person1",$person->name);
        $person = $book->getPerson(1);
        $this->assertEquals("Person2",$person->getName());
        $this->assertEquals("Person2",$person->name);
        $person = $book->getPerson(2);
        $this->assertEquals("Person3",$person->getName());
        $this->assertEquals("Person3",$person->name);
        $person = $book->getPerson(3);
        $this->assertEquals("Jeffrey",$person->getName());
        $this->assertEquals("Jeffrey",$person->name);
        $this->assertEquals(2,$person->getPhoneCount());
        $this->assertEquals("5195551234",$person->getPhone(0)->getNumber());
        $this->assertEquals("5195551234",$person->phone(0)->number);
        $this->assertEquals(Unit_Test_Person_PhoneType::MOBILE,$person->getPhone(0)->getType());
        $this->assertEquals(Unit_Test_Person_PhoneType::MOBILE,$person->phone(0)->type);
        $this->assertEquals("5195552345",$person->getPhone(1)->getNumber());
        $this->assertEquals("5195552345",$person->phone(1)->number);
        $this->assertEquals(Unit_Test_Person_PhoneType::WORK,$person->getPhone(1)->getType());
        $this->assertEquals(Unit_Test_Person_PhoneType::WORK,$person->phone(1)->type);

        
        $pbData = $book->__toString();
        $this->assertNotEmpty((string)$book);
        $this->assertEquals((string)$book,$pbData);

         # Serialize the data to a .pb format.
        $tmp = dirname(__FILE__) . '/addressbook.pb';
				$outfp = fopen($tmp, 'wb');
        $book->write($outfp);
        fclose($outfp);

        $this->assertTrue(filesize($tmp) > 0);

				// check that the string output is correct
				$string = $book->getPbString();
				$this->assertEquals($string,file_get_contents($tmp));

				$tmp = 'addressbook.py.pb';
        // Read it into a new addressbook
        $infp = fopen($tmp, 'rb');
        $newBook = new Unit_Test_AddressBook($infp);
				fclose($infp);		
				//unlink($tmp);
		
        # Re-run some tests on the new book to ensure things match.
        $this->assertEquals(4,$newBook->getPersonCount());
        $this->assertEquals($book->getPerson(0)->getName(),$newBook->getPerson(0)->getName());
        $this->assertEquals($book->getPerson(0)->getNegative(),$newBook->getPerson(0)->getNegative());
        $this->assertEquals($book->getPerson(1)->getName(),$newBook->getPerson(1)->getName());
        $this->assertEquals($book->getPerson(2)->getName(),$newBook->getPerson(2)->getName());
        $this->assertEquals($book->getPerson(3)->getName(),$newBook->getPerson(3)->getName());

        $person = $book->getPerson(3);
        $this->assertEquals($book->getPerson(3)->getPhoneCount(),$newBook->getPerson(3)->getPhoneCount());
        $this->assertEquals($book->getPerson(3)->getPhone(0)->getNumber(),$newBook->getPerson(3)->getPhone(0)->getNumber());
        $this->assertEquals($book->getPerson(3)->getPhone(0)->getType(),$newBook->getPerson(3)->getPhone(0)->getType());
        $this->assertEquals($book->getPerson(3)->getPhone(1)->getNumber(),$newBook->getPerson(3)->getPhone(1)->getNumber());
        $this->assertEquals($book->getPerson(3)->getPhone(1)->getType(),$newBook->getPerson(3)->getPhone(1)->getType());
        
        
          
    }
    
    
}
