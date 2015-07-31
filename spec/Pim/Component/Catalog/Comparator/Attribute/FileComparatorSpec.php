<?php

namespace spec\Pim\Component\Catalog\Comparator\Attribute;

use Akeneo\Component\FileStorage\Model\FileInterface;
use Akeneo\Component\FileStorage\Repository\FileRepositoryInterface;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class FileComparatorSpec extends ObjectBehavior
{
    function let(FileRepositoryInterface $repository)
    {
        $this->beConstructedWith(['pim_catalog_file', 'pim_catalog_file'], $repository);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Pim\Component\Catalog\Comparator\Attribute\FileComparator');
    }

    function it_finds_a_diff_when_there_was_no_original_file()
    {
        $this->compare(
            ['data' => ['filePath' => 'path/to/my/file.txt']],
            ['data' => ['filePath' => null]]
        )->shouldReturn(['data' => ['filePath' => 'path/to/my/file.txt']]);
    }

    function it_finds_a_diff_when_file_the_is_deleted($repository, FileInterface $file)
    {
        $file->getHash()->willReturn('hash');
        $repository->findOneByIdentifier('key/of/my/original/file.txt')->willReturn($file);

        $this->compare(
            ['data' => ['filePath' => null]],
            ['data' => ['filePath' => 'key/of/my/original/file.txt']]
        )->shouldReturn(['data' => ['filePath' => null]]);
    }

    function it_finds_a_diff_when_files_are_different($repository, FileInterface $file)
    {
        $file->getHash()->willReturn('hash');
        $repository->findOneByIdentifier('key/of/my/original/file.txt')->willReturn($file);

        $this->compare(
            ['data' => ['filePath' => __FILE__]],
            ['data' => ['filePath' => 'key/of/my/original/file.txt']]
        )->shouldReturn(['data' => ['filePath' => __FILE__]]);
    }

    function it_returns_null_when_there_is_no_diff($repository, FileInterface $file)
    {
        $file->getHash()->willReturn(sha1_file(__FILE__));
        $repository->findOneByIdentifier('key/of/my/original/file.txt')->willReturn($file);

        $this->compare(
            ['data' => ['filePath' => __FILE__]],
            ['data' => ['filePath' => 'key/of/my/original/file.txt']]
        )->shouldReturn(null);
    }
}
