<?

require_once 'lib/FlatFile.php';
require_once 'lib/FlatFileDB.php';

class FlatFileDBTest extends PHPUnit_Framework_TestCase {

	private $db = FALSE;
	private $obj1;

	public function testQdbm() {
		$drivers = dba_handlers(FALSE);
		$this->assertContains( FlatFileDB::HANDLER, $drivers );
	}

	public function setUp() {
		ini_set('error_reporting', E_ALL & ~E_NOTICE);
		$this->db = FlatFile::open('db/cms.qdbm');
		ini_set('error_reporting', E_ALL );
		
		// a test object
		$this->obj1 = new stdClass;
		$this->obj1->title='Title';
		$this->obj1->body='<p>€ 3.599,00'.PHP_EOL.'<b>§@#!</b></p>';
	}

	public function tearDown() {
		FlatFile::close('db/cms.qdbm');
	}

	public static function tearDownAfterClass()
	{
		unlink('db/cms.qdbm');
		unlink('db/cms.qdbm.lck');
	}


	public function testOpen() {
	  $this->assertTrue(TRUE);
	}

	public function testSetObject() {
	  $this->assertTrue( $this->db->set('object',$this->obj1) );
	}

	public function testValid() {
	  $this->assertTrue( $this->db->is_valid('object') );
	}

	public function testGetObject() {
		$obj1 = $this->db->get('object');
		$this->assertEquals($this->obj1,$obj1);
	}

	public function testDelete() {
	  $this->assertTrue( $this->db->set('delete','delete') );
	  $this->assertTrue( $this->db->delete('delete') );
	}

	public function testGetAll() {
		$data = $this->db->get_all();
		$this->assertInternalType('array',$data);
	}

	public function testReopen() {
		$db = FlatFile::open('db/cms.qdbm');
		$this->assertSame($this->db,$this->db);
	}

}
