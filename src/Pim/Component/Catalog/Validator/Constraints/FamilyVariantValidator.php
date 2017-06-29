<?php

namespace Pim\Component\Catalog\Validator\Constraints;

use Doctrine\Common\Collections\Collection;
use Pim\Component\Catalog\Model\AttributeInterface;
use Pim\Component\Catalog\Model\FamilyVariantInterface;
use Symfony\Component\Translation\TranslatorInterface;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;

/**
 * Family variant validator, it checks that attribute sets are well built.
 *
 * @author    Arnaud Langlade <arnaud.langlade@akeneo.com>
 * @copyright 2017 Akeneo SAS (http://www.akeneo.com)
 * @license   http://opensource.org/licenses/osl-3.0.php  Open Software License (OSL 3.0)
 */
class FamilyVariantValidator extends ConstraintValidator
{
    /** @var TranslatorInterface */
    private $translator;

    /** @var array */
    private $availableTypes;

    public function __construct(TranslatorInterface $translator, array $availableTypes)
    {
        $this->translator = $translator;
        $this->availableTypes = $availableTypes;
    }

    /**
     * {@inheritdoc}
     *
     * @param FamilyVariantInterface $familyVariant
     */
    public function validate($familyVariant, Constraint $constraint): void
    {
        if (!$familyVariant instanceof FamilyVariantInterface) {
            throw new UnexpectedTypeException($constraint, FamilyVariantInterface::class);
        }

        if (!$constraint instanceof FamilyVariant) {
            throw new UnexpectedTypeException($constraint, FamilyVariant::class);
        }

        $this->validateAxes($familyVariant->getAxes());
        $this->validateAttributes($familyVariant->getAttributes());
    }

    /**
     * Validate the attribute set attributes
     *
     * @param Collection $attributes
     */
    private function validateAttributes(Collection $attributes): void
    {
        $attributeCodes = $attributes->map(function (AttributeInterface $attribute) {
            return $attribute->getCode();
        })->toArray();

        if (count($attributeCodes) !== count(array_unique($attributeCodes))) {
            $message = $this->translator->trans('pim_catalog.constraint.family_variant_attributes_unique');
            $this->context->buildViolation($message)->addViolation();
        }
    }

    /**
     * Validate the attribute set axis
     *
     * @param Collection $axes
     */
    private function validateAxes(Collection $axes): void
    {
        $axisCodes = [];
        foreach ($axes as $axis) {
            $axisCodes[] = $axis->getCode();
            if (!in_array($axis->getType(), $this->availableTypes)) {
                $message = $this->translator->trans('pim_catalog.constraint.family_variant_axes_type');
                $this->context->buildViolation($message)->addViolation();
            }
        }

        if (count($axisCodes) !== count(array_unique($axisCodes))) {
            $message = $this->translator->trans('pim_catalog.constraint.family_variant_axes_unique');
            $this->context->buildViolation($message)->addViolation();
        }
    }
}
