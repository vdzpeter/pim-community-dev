<?php

namespace spec\Pim\Component\Catalog\Manager;

use Akeneo\Component\StorageUtils\Saver\BulkSaverInterface;
use Akeneo\Component\StorageUtils\Saver\SaverInterface;
use PhpSpec\ObjectBehavior;
use Pim\Component\Catalog\Model\AttributeGroupInterface;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Repository\AttributeGroupRepositoryInterface;

class AttributeGroupManagerSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Component\Catalog\Manager\AttributeGroupManager');
    }

    function let(
        AttributeGroupRepositoryInterface $repository,
        SaverInterface $groupSaver,
        BulkSaverInterface $attributeSaver
    ) {
        $this->beConstructedWith($repository, $groupSaver, $attributeSaver);
    }

    function it_throws_an_exception_when_removing_an_attribute_group_and_the_default_group_does_not_exist(
        $repository,
        AttributeGroupInterface $group,
        AttributeInterface $attribute
    ) {
        $repository->findDefaultAttributeGroup()->willReturn(null);

        $this->shouldThrow(new \LogicException('The default attribute group should exist.'))
            ->during('removeAttribute', [$group, $attribute]);
    }

    function it_add_attributes_to_attribute_group(
        $groupSaver,
        $attributeSaver,
        AttributeGroupInterface $default,
        AttributeGroupInterface $group,
        AttributeInterface $sku,
        AttributeInterface $name
    ) {
        $group->getMaxAttributeSortOrder()->willReturn(5);

        $sku->setSortOrder(6)->shouldBeCalled();
        $group->addAttribute($sku)->shouldBeCalled();

        $name->setSortOrder(7)->shouldBeCalled();
        $group->addAttribute($name)->shouldBeCalled();

        $attributeSaver->saveAll([$sku, $name])->shouldBeCalled();
        $groupSaver->save($group)->shouldBeCalled();

        $this->addAttributes($group, [$sku, $name]);
    }
}
