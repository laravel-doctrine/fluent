<?php

namespace Tests\Relations\Traits;

trait NonPrimary
{
    public function test_can_not_set_primary_key()
    {
        $this->assertFalse($this->relation->getBuilder()->getClassMetadata()->isIdentifier($this->field));

        $this->setExpectedException(
            'Doctrine\ORM\Mapping\MappingException',
            'Many-to-many or one-to-many associations are not allowed to be identifier in \'Tests\Relations\FluentEntity#' . $this->field . '\''
        );

        $this->relation->makePrimaryKey();
        $this->relation->build();
    }
}
