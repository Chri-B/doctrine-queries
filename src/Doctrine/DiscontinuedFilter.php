<?php

namespace App\Doctrine;

use App\Entity\FortuneCookie;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;

class DiscontinuedFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if($targetEntity->name !== FortuneCookie::class) {
            return '';
        }
        return sprintf('%s.discontinued = %s', $targetTableAlias, $this->getParameter('discontinued')); // getParameter fa escape del parametro (altrimenti questo codice potrebbe essere vulnerabile a sql injection)
    }
}