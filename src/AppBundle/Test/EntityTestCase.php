<?php
namespace AppBundle\Test;

/**
 * EntityTestCase
 */
class EntityTestCase extends MainTestCase
{
    
    /**
     * エンティティのテストを実行する
     * @param string $entityName テストしたいエンティティ名
     */
    public function entityCheckString($entityName) {
        $object = new $entityName;
        $this->entityCheck($object);
    }
    
    
    /**
     * エンティティオブジェクトのテスト
     * @param Object $entity
     */
    public function entityCheck($entity) {
        $em = $this->getEntityManager();
        $cmd = $em->getMetadataFactory();
        $meta = $cmd->getMetadataFor(get_class($entity));
        $this->assertTrue(true);
        
        foreach ($meta->fieldMappings  as $key => $value) {
            $expect = $this->getValue($value['type']);
            $method = ucfirst($key);
            if($this->callEntityMethod($entity, "set".$method, $expect)) {
                $actual = $this->callEntityMethod($entity, "get".$method, $expect);
                $this->assertEquals($expect, $actual);
            }
        }
    }
    
    
    protected function getValue($type) {
        switch ($type) {
            case "datetime":
                $expect = new \DateTime;
                break;
            case "integer":
                $expect = 100;
                break;
            case "float":
                $expect = 1.1;
                break;
            default:
                $expect = "abc";
                break;
        }       
    }
    
    protected function callEntityMethod($entity,$method,$param) {
        if (method_exists($entity, $method)) {
            return call_user_func(array($entity,$method),$param);
        }
        return false;
    }
}

