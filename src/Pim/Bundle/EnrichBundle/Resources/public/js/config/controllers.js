define([
    "pim/controller/association-type",
    "pim/controller/common/index",
    "pim/controller/form",
    "pim/controller/channel/edit",
    "pim/controller/group-type",
    "pim/controller/product",
    "pim/controller/group",
    "pim/controller/variant-group",
    "pim/controller/job-instance",
    "pim/controller/redirect",
    "pim/controller/template",
    "pim/controller/family",
    "pim/controller/user",
    "pim/controller/role",
    "pim/controller/job-execution",
    "pim/controller/system"
], function(AssociationType, Index, Form, Edit, GroupType, Product, Group, VariantGroup, JobInstance, Redirect, Template, Family, User, Role, JobExecution, System) {
    return {
        "pim/controller/association-type": AssociationType,
        "pim/controller/common/index": Index,
        "pim/controller/form": Form,
        "pim/controller/channel/edit": Edit,
        "pim/controller/group-type": GroupType,
        "pim/controller/product": Product,
        "pim/controller/group": Group,
        "pim/controller/variant-group": VariantGroup,
        "pim/controller/job-instance": JobInstance,
        "pim/controller/redirect": Redirect,
        "pim/controller/template": Template,
        "pim/controller/family": Family,
        "pim/controller/user": User,
        "pim/controller/role": Role,
        "pim/controller/job-execution": JobExecution,
        "pim/controller/system": System
    }
})