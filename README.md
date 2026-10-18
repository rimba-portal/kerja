# kerja

10. Seed Contract Freeze
The complete domain-package JSON contract is:

{
  "org_units": [],
  "org_teams": [],
  "job_roles": [],

  "activity_types": [],
  "business_objects": [],

  "lifecycles": [],
  "lifecycle_phases": [],

  "work_packages": [],
  "work_flows": [],

  "endpoints": [],
  "hooks": [],
  "hook_bindings": []
}

Import order remains:

OrganizationImporter
CatalogImporter
PackageImporter
FlowImporter
HookImporter
FlowDefinitionValidator

The existing split importer design is retained.