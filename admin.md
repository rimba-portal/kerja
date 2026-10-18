# Rimba/Kerja Administrator Guide

## 1. Purpose

`rimba/kerja` is the generic lifecycle and work-orchestration engine for the Rimba ecosystem.

Kerja manages:

- Lifecycle definitions and lifecycle phases
- Flows, FlowSteps, and FlowActions
- Packages and their SIPOC-style suppliers, inputs, outputs, and customers
- FlowInstances and runtime Tasks
- task claiming, assignment, start, completion, cancellation, and withdrawal
- FormData and Traveller runtime data
- lifecycle tracking and lifecycle transitions
- integration Endpoints, Hooks, and HookBindings
- import of complete work catalogs from JSON

Kerja does **not** own the business-domain forms or business-domain models. Those belong to packages such as `rimba/dms`, `rimba/eam`, or `rimba/hrm`.

For example:

```text
rimba/dms
    owns Document, DocumentVersion, document forms and document API

rimba/kerja
    owns Document Lifecycle definitions, Flows, Packages, Tasks and execution
```

---

# 2. Core Vocabulary

## Lifecycle

A Lifecycle describes the meaningful states through which a BusinessObject progresses.

Example:

```text
Document Lifecycle

Create
  -> Review
  -> Approve
  -> Publish
  -> Archive
```

A Lifecycle is not a procedure. It describes state progression.

## LifecyclePhase

A LifecyclePhase is one state within a Lifecycle.

Examples:

```text
Create
Review
Approve
Publish
Archive
```

A phase may have zero, one, or multiple Flows associated with it.

## Flow

A Flow is an executable procedure performed while a BusinessObject is associated with a LifecyclePhase.

Example:

```text
Document Review Flow

Review Document
    -> Verify -> Complete
    -> Return -> Revise -> Review
    -> Cancel -> Cancelled
```

## FlowStep

A FlowStep is one stage in a Flow.

Each FlowStep references one Package.

A FlowStep has one StepType:

```text
submit
verify
acknowledge
approve
```

## FlowAction

A FlowAction describes what may happen when work on a FlowStep is completed.

Examples:

```text
submit
verify
proceed
approve
reject
return
cancel
```

`Withdraw` is handled at FlowInstance level rather than as an ordinary Step action.

A FlowAction must either:

```text
route to another FlowStep
```

or:

```text
terminate the FlowInstance
```

but not both.

## Package

A Package describes an assignable unit of work.

It identifies:

```text
what work is done
which OrgTeam owns the work
which JobRole performs the work
which ActivityType describes the work
which BusinessObject the work concerns
its suppliers
its inputs
its outputs
its customers
```

A Package is a reusable definition. A Task is its runtime assignment.

## Task

A Task is created when a FlowInstance enters a FlowStep.

Typical runtime progression:

```text
READY
  -> claimed
ASSIGNED
  -> started
STARTED
  -> FlowAction
COMPLETED
```

## FlowInstance

A FlowInstance is one runtime execution of a Flow.

It stores:

```text
Flow definition/version
initiator
subject
current FlowStep
status
FormData
Traveller
runtime timestamps
```

## FormData

Kerja stores `form_data`, but Kerja does not define the form itself.

The domain package owns:

```text
form fields
Filament schema/components
validation
layout
domain interaction
```

## Traveller

Traveller stores persistent working context that moves with the FlowInstance.

For example:

```json
{
  "document": {
    "id": 100,
    "version": "1.2",
    "status": "review"
  }
}
```

## Endpoint

An Endpoint describes a secured external/domain service connection.

It contains configuration such as:

```text
code
base URL
authentication profile
timeout
active state
```

Secrets are kept in application configuration rather than Hook JSON.

## Hook

A Hook performs one integration/data operation.

Supported operations are:

```text
GET
TRANSFORM
PUT
```

## HookBinding

A HookBinding attaches a Hook to a Flow, FlowStep, or FlowAction at a supported execution point.

Current HookPoints are:

```text
workflow_load
step_enter
step_exit
action_exit
```

---

# 3. Definition Data vs Runtime Data

Administrators should distinguish between definitions and executions.

## Definition records

```text
ActivityType
BusinessObject
Lifecycle
LifecyclePhase
Package
PackageParty
PackagePayload
Flow
FlowOwner
FlowInitiator
FlowStep
FlowAction
Endpoint
Hook
HookBinding
```

These describe how work should operate.

## Runtime records

```text
FlowInstance
LifecycleTracking
Task
TaskEvent
Artifact
```

These records are produced by execution of the engine and should not normally be manually created.

---

# 4. Recommended Sources of Definitions

Kerja supports two practical sources of definitions.

## Package-owned JSON catalogs

Use JSON when a reusable Rimba business-domain package owns the definition.

Example:

```text
rimba/dms/resources/seeds/document-lifecycle.json
```

A domain package should ship all reusable lifecycle definitions required by that domain.

Examples:

```text
rimba/dms
    Document Lifecycle

rimba/eam
    Asset Lifecycle
    Equipment Lifecycle
    Maintenance Lifecycle

rimba/hrm
    Employee Lifecycle
    Recruitment Lifecycle
    Onboarding Lifecycle
```

JSON package catalogs should be treated as the reusable baseline definition.

## Manual administration

Manual administration is appropriate for local/company-specific definitions that do not belong in a reusable domain package.

Examples include:

```text
site-specific approval Flow
local internal request
temporary operational process
company-specific Package
```

Avoid casually modifying a package-owned definition in the database if the JSON catalog remains its source definition, because a later catalog import may reconcile that data again.

---

# 5. Complete JSON Catalog Structure

Kerja accepts a catalog structured as:

```json
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
```

The sections are imported in dependency order.

---

# 6. Organizational Bootstrap

A catalog may declare the OrgUnits, OrgTeams, and JobRoles required by the lifecycle.

Example:

```json
{
  "org_units": [
    {
      "code": "INFORMATION_MANAGEMENT",
      "name": "Information Management"
    }
  ],
  "org_teams": [
    {
      "code": "DOCUMENT_CONTROL",
      "name": "Document Control",
      "org_unit": "INFORMATION_MANAGEMENT"
    }
  ],
  "job_roles": [
    {
      "code": "DOCUMENT_REVIEWER",
      "name": "Document Reviewer",
      "type": "operation"
    }
  ]
}
```

Kerja uses this section to bootstrap vocabulary needed by the catalog. Organization/position data remains owned by the corresponding organization and job-role packages.

Existing organizational records are not intended to be treated as Kerja-owned master data.

---

# 7. Activity Types

ActivityType provides a reusable description of the type of work being performed.

The built-in Kerja seeder includes activity types such as:

```text
creates
captures
verifies
analyzes
decides
authorizes
transforms
executes
records
informs
```

A domain catalog may also declare the activity types it requires.

Example:

```json
{
  "code": "verifies",
  "name": "Verify",
  "description": "Check information or work for correctness, completeness and required criteria.",
  "is_active": true
}
```

---

# 8. Business Objects

A BusinessObject identifies what the lifecycle and work operate on.

Example:

```json
{
  "code": "document",
  "name": "Document",
  "model_class": null,
  "description": "A controlled document managed through the Document Lifecycle.",
  "is_active": true
}
```

During Kerja-only testing, `model_class` may be null.

When a domain package provides the real model, it may point to that domain model class.

Example conceptually:

```text
Document -> DMS Document model
Asset    -> EAM Asset model
Employee -> HRM Employee model
```

---

# 9. Creating a Lifecycle

Define the Lifecycle first:

```json
{
  "code": "ikd",
  "name": "Document Lifecycle",
  "business_object": "document",
  "description": "Manages controlled documents from creation through archival."
}
```

Then define its phases:

```json
{
  "code": "ikd-review",
  "name": "Review",
  "lifecycle": "ikd",
  "sequence": 2,
  "functional_role": "DOCUMENT_REVIEWER",
  "description": "Document is in review."
}
```

Phase sequence determines ordering for `advance` lifecycle transitions.

---

# 10. Creating a Package

A Package is the reusable definition of work performed at a FlowStep.

Example:

```json
{
  "code": "document-review",
  "name": "Review Document",
  "description": "Review document content for correctness and completeness.",
  "org_team": "DOCUMENT_CONTROL",
  "responsible_job_role": "DOCUMENT_REVIEWER",
  "activity_type": "verifies",
  "business_object": "document",
  "version": 1,
  "status": "active",
  "suppliers": [
    "Document Author"
  ],
  "inputs": [
    {
      "code": "draft-document",
      "name": "Draft Document",
      "data_type": "mixed",
      "is_required": true
    }
  ],
  "outputs": [
    {
      "code": "reviewed-document",
      "name": "Reviewed Document",
      "data_type": "mixed",
      "is_required": true
    }
  ],
  "customers": [
    "Document Approver"
  ]
}
```

## Package responsibility

`org_team` defines the organizational team responsible for the Package definition.

`responsible_job_role` identifies the JobRole used by current Task eligibility logic.

The current task-eligibility implementation matches the actor's JobRoles against the Package's `actor_job_role_id`.

---

# 11. Creating a Flow

Example:

```json
{
  "code": "document-review",
  "name": "Document Review",
  "business_object": "document",
  "lifecycle_phase": "ikd-review",
  "lifecycle_transition_type": "advance",
  "version": 1,
  "status": "active",
  "owner_roles": [
    "DOCUMENT_REVIEWER"
  ],
  "initiator_roles": [
    "DOCUMENT_AUTHOR",
    "DOCUMENT_REVIEWER"
  ],
  "steps": []
}
```

An active Flow is validated after the complete catalog graph, including Hooks and HookBindings, has been imported.

---

# 12. Lifecycle Transition Types

Every Flow may define what successful completion does to LifecycleTracking.

## `stay`

```json
"lifecycle_transition_type": "stay"
```

The subject remains in its current LifecyclePhase.

Use this when completing the Flow should not change lifecycle state.

Example:

```text
Document Publication completes
Document remains Published
```

## `advance`

```json
"lifecycle_transition_type": "advance"
```

Kerja moves LifecycleTracking to the next phase by phase sequence.

Example:

```text
Create -> Review
Review -> Approve
Approve -> Publish
```

## `transition`

```json
"lifecycle_transition_type": "transition",
"next_lifecycle_phase": "ikd-archive"
```

Successful completion explicitly moves the subject to the configured phase.

Example:

```text
Document currently Published
Archival Flow completes
Document -> Archive
```

`next_lifecycle_phase` is only valid with `transition`.

Rejected, cancelled, withdrawn, or failed FlowInstances do not execute the normal successful lifecycle transition.

---

# 13. Creating FlowSteps

Example:

```json
{
  "code": "review",
  "name": "Review Document",
  "step_type": "verify",
  "work_package": "document-review",
  "sequence": 1,
  "is_start": true,
  "actions": []
}
```

Each active Flow must have exactly one start Step.

A FlowStep must reference an existing Package.

---

# 14. Step Types and Allowed Actions

Kerja currently defines four StepTypes.

## Submit

```text
submit
```

Allowed FlowAction:

```text
submit
```

## Verify

```text
verify
```

Allowed FlowActions:

```text
verify
return
cancel
```

## Acknowledge

```text
acknowledge
```

Allowed FlowActions:

```text
proceed
return
cancel
```

## Approve

```text
approve
```

Allowed FlowActions:

```text
approve
reject
return
```

`withdraw` is not configured as an ordinary Step action.

---

# 15. FlowAction Routing

A FlowAction must define exactly one of:

```text
target_step
```

or:

```text
terminal_status
```

## Route to another Step

```json
{
  "action_type": "return",
  "target_step": "revise",
  "sequence": 2,
  "requires_confirmation": true
}
```

## Complete the Flow

```json
{
  "action_type": "verify",
  "terminal_status": "completed",
  "sequence": 1
}
```

Supported FlowInstance statuses include:

```text
active
completed
rejected
cancelled
withdrawn
failed
```

`active` cannot be used as a terminal status.

---

# 16. Flow Owners and Initiators

## Owner roles

```json
"owner_roles": [
  "DOCUMENT_REVIEWER"
]
```

These are stored as FlowOwner definitions.

## Initiator roles

```json
"initiator_roles": [
  "DOCUMENT_AUTHOR",
  "DOCUMENT_REVIEWER"
]
```

Kerja uses the current actor's JobRoles to determine whether the actor may start the Flow.

An initiator entry may also include an OrgTeam in the JSON structure supported by the importer.

---

# 17. Importing a Catalog Directly

Kerja provides the direct catalog command:

```bash
php artisan rimba:seed path/to/catalog.json
```

The command invokes `WorkSeedImportService` and reports imported activity types, business objects, Packages, and Flows.

Use the direct command for:

```text
development
testing
one-off catalog validation
manually supplied catalog files
```

The import is executed within a database transaction.

If a required catalog reference is missing or an active Flow fails validation, the import fails rather than committing a partially valid definition graph.

---

# 18. Registered Package Catalogs

Reusable domain packages can register lifecycle catalogs in Kerja's `CatalogRegistry`.

The registered catalogs can then be imported with:

```bash
php artisan kerja:catalog-import
```

To import one registered catalog:

```bash
php artisan kerja:catalog-import rimba/dms.document
```

The exact catalog key is the key registered by the domain package.

Registration itself should only make the catalog known to Kerja. Database mutation happens when the catalog import is explicitly run.

---

# 19. Catalog Import Pipeline

The importer is deliberately split by responsibility.

```text
WorkSeedImportService
       |
       +-- OrganizationImporter
       |     OrgUnit
       |     OrgTeam
       |     JobRole
       |
       +-- CatalogImporter
       |     ActivityType
       |     BusinessObject
       |     Lifecycle
       |     LifecyclePhase
       |
       +-- PackageImporter
       |     Package
       |     PackageParty
       |     PackagePayload
       |
       +-- FlowImporter
       |     Flow
       |     FlowOwner
       |     FlowInitiator
       |     FlowStep
       |     FlowAction
       |
       +-- HookImporter
       |     Endpoint
       |     Hook
       |     HookBinding
       |
       +-- FlowDefinitionValidator
```

This dependency order must be preserved.

---

# 20. Updating an Existing Catalog

The importers use code-based lookup and update-or-insert logic for the main definition records.

Use stable codes.

Good:

```text
document-review
ikd-review
DOCUMENT_REVIEWER
```

Avoid changing a code merely to change its display name.

Instead modify fields such as:

```text
name
description
sequence
status
```

and re-import the catalog.

For Flow Steps, the Flow importer synchronizes the declared Steps and their Actions. Definitions removed from the JSON may therefore be removed from the imported Flow structure.

Treat a package-owned JSON catalog as controlled configuration and review catalog changes before importing them into a production environment.

---

# 21. Task Runtime

When a Flow starts:

```text
1. Kerja verifies that the initiator is allowed to start the Flow.
2. Kerja locates the Flow's start Step.
3. A FlowInstance is created.
4. LifecycleTracking is established when a subject and LifecyclePhase are available.
5. workflow_load Hooks run.
6. step_enter Hooks run.
7. A READY Task is created for the start Step's Package.
```

A Task is not automatically assigned to an arbitrary Staff member.

---

# 22. Claiming a Task

A ready and unassigned Task may be claimed by an eligible actor.

Current eligibility uses the Package's responsible JobRole.

Conceptually:

```text
Package responsible JobRole
        |
        v
Staff JobRoles
        |
      match?
        |
        v
actor may claim Task
```

Claiming changes:

```text
ready -> assigned
```

and records the assignee and `assigned_at` timestamp.

---

# 23. Starting a Task

Only the Task assignee can start an assigned Task.

```text
assigned -> started
```

Kerja records `started_at` and a TaskEvent.

---

# 24. Performing Domain Work

The domain package owns the actual work interface.

Kerja's `TaskRendererRegistry` maps a Package code to a domain-owned renderer class.

Conceptual example:

```text
document-review
      |
      v
DMS ReviewDocument renderer
```

The domain renderer is responsible for domain-specific fields and validation.

Kerja remains responsible for:

```text
Task context
FlowInstance
FormData persistence
Traveller
available FlowActions
Action execution
Hooks
routing
```

---

# 25. Executing a FlowAction

An actor may execute a FlowAction only when:

```text
the action belongs to the Task's current FlowStep
the StepType allows the ActionType
the actor is the Task assignee
the Task is assigned or started
```

Execution may:

```text
save FormData
run step_exit Hooks
run action_exit Hooks
complete the current Task
route to another Step and create its Task
```

or terminate the FlowInstance.

On successful `completed` termination, Kerja invokes the Lifecycle transition behavior configured on the Flow.

---

# 26. Withdrawing a Flow

Withdraw is a requestor-level action against the FlowInstance.

It is not a normal FlowStep action.

A withdrawal:

```text
requires an active FlowInstance
requires the requestor/initiator
cancels open Tasks
sets FlowInstance status to withdrawn
records withdrawn_at
records an event
```

---

# 27. Hooks

Hooks allow Kerja to exchange runtime data with domain packages or other services.

## GET

Retrieves data from an Endpoint into FormData or Traveller.

```text
Endpoint -> response -> mappings -> form / traveller
```

## TRANSFORM

Transforms values within Kerja runtime context.

Supported converter names currently include:

```text
copy
string
integer
decimal
boolean
date
datetime
add
subtract
```

## PUT

Builds a request from runtime data and sends it to an Endpoint.

```text
form / traveller / context / parameter
          |
          v
       request
          |
          v
       Endpoint
```

Response mappings may write values back to FormData or Traveller.

---

# 28. Hook Paths

Hook mapping paths may use:

```text
form.*
traveller.*
request.*
response.*
context.*
parameter.*
```

Examples:

```text
form.comments
traveller.document.id
response.status
context.subject.id
context.actor.id
parameter.document_type
```

Endpoint paths may interpolate allowed context/parameter paths.

Example:

```text
/documents/{context.subject.id}
```

---

# 29. Hook Failure Behavior

A required Hook failure stops progression by throwing the error.

An optional Hook failure is logged and does not stop execution.

Administrators should use `is_required` carefully:

```text
Required
    domain consistency depends on successful execution

Optional
    useful enrichment/integration but Flow may continue without it
```

---

# 30. Endpoint Authentication

Endpoint database records may refer to an `auth_profile`.

Actual credentials belong in application configuration.

Example configuration shape:

```php
'endpoint_auth' => [
    'internal-service' => [
        'type' => 'bearer',
        'token' => env('KERJA_INTERNAL_SERVICE_TOKEN'),
    ],
],
```

Supported authentication profile types in the current client are:

```text
bearer
basic
header
```

Do not place passwords, access tokens, or other secrets in lifecycle JSON catalogs.

---

# 31. Document Lifecycle Example

The Kerja-only Document seed currently demonstrates:

```text
Create
  |
  | document-authoring completes: ADVANCE
  v
Review
  |
  | document-review completes: ADVANCE
  v
Approve
  |
  | document-approval completes: ADVANCE
  v
Publish
  |
  | document-publication completes: STAY
  |
  | document remains published
  |
  | document-archival completes: TRANSITION
  v
Archive
```

This demonstrates why LifecyclePhase and Flow are separate concepts.

A BusinessObject can remain in a phase while multiple Flows execute there.

---

# 32. Admin Resources

Definition resources are intended to inspect and, where enabled, administer definition data such as:

```text
Activity Types
Business Objects
Lifecycles
Lifecycle Phases
Packages
Flows
Flow Steps
Flow Actions
Flow Initiators
Endpoints
Hooks
Hook Bindings
```

Runtime resources such as Tasks, TaskEvents, Artifacts and FlowInstances should be treated primarily as operational records produced by the engine rather than manually created records.

Some resource screens in the current package are still intentionally minimal and should not be interpreted as the domain-specific Task form UI.

---

# 33. Operational Checks After Import

After importing a catalog, verify at minimum:

```text
Lifecycle exists
LifecyclePhase ordering is correct
Package references resolve
Flow has exactly one start Step
Step Types and Actions are compatible
Action targets remain within the same Flow
terminal Actions have valid terminal status
transition Flows have a valid target LifecyclePhase
Endpoint references resolve when Hooks require them
HookBindings point to valid Flow/Step/Action definitions
```

For test/development environments, run the application's Kerja test suite after definition changes.

---

# 34. Troubleshooting Catalog Imports

## Missing catalog reference

Example condition:

```text
Package references a JobRole, BusinessObject, ActivityType, or OrgTeam code that does not exist.
```

Correction:

```text
Declare the referenced catalog item earlier in the same JSON catalog
or ensure it already exists in the owning system/package.
```

## Flow validation fails

Check:

```text
one and only one start Step
every Step has a Package
every Step has at least one Action
ActionType is permitted for StepType
Action has target Step XOR terminal status
target Step belongs to same Flow
transition target belongs to same Lifecycle
required Endpoint exists and is active
```

## Task cannot be claimed

Check:

```text
Task status is ready
Task is unassigned
actor has the Package's responsible JobRole
```

## Hook call fails

Check:

```text
Endpoint active
base URL correct
auth profile configured
Hook path configured
HTTP method supported
mapping paths valid
required context/parameter value exists
```

---

# 35. Governance Recommendations

For stable operation, use the following administrative rules:

1. Treat codes as stable identifiers after a definition has been deployed.
2. Keep reusable domain definitions with their owning Rimba package.
3. Use manual definitions primarily for local/company-specific work.
4. Keep domain forms and validation outside Kerja.
5. Keep credentials outside catalogs.
6. Use `draft` while constructing definitions and `active` only when the Flow passes validation.
7. Review lifecycle transition behavior explicitly for every active Flow.
8. Preserve the distinction between Package definition and runtime Task.
9. Preserve the distinction between Lifecycle state and Flow procedure.
10. Test package catalog changes before importing them into production.

---

# 36. Domain Package Integration Pattern

A domain package should eventually look conceptually like:

```text
rimba/dms
|
+-- Models/
|   +-- Document.php
|   +-- ...
|
+-- Actions/
|
+-- HTTP / API /
|
+-- Filament /
|   +-- domain forms and Task renderers
|
+-- resources/seeds/
|   +-- document-lifecycle.json
|
+-- DmsServiceProvider.php
```

Its ServiceProvider may register the package catalog and Task renderers with Kerja.

Kerja then remains unchanged when another domain package is installed.

---

# 37. Administrator Mental Model

Use this model when deciding where something belongs:

```text
BusinessObject
    What thing are we managing?

Lifecycle
    What meaningful states does that thing pass through?

Flow
    What procedure is performed while it is in a state?

FlowStep
    What stage of that procedure are we at?

Package
    What assignable work must somebody perform?

Task
    Who is performing that work now?

FlowAction
    What did the actor decide/do, and where does execution go next?

Hook
    What data must Kerja read, transform, or send at this point?
```

If an administrator can answer these questions distinctly, the definition is normally being modeled at the correct level.

---

# 38. Current Known Boundary

The current engine can define and import complete Lifecycle, Package, Flow, Step, Action, Endpoint, Hook and HookBinding graphs and supports Task claiming/start/action execution.

The domain-specific Task interaction remains a package integration responsibility through `TaskRendererRegistry`.

The generic Kerja admin/runtime UI should not be used as a replacement for a domain-owned work form.

---

# 39. Checklist for a New Lifecycle Catalog

Before activating a new catalog:

```text
[ ] BusinessObject is defined
[ ] Lifecycle is defined
[ ] LifecyclePhases are ordered
[ ] required OrgUnits exist or are bootstrapped
[ ] required OrgTeams exist or are bootstrapped
[ ] required JobRoles exist or are bootstrapped
[ ] required ActivityTypes exist
[ ] Packages are defined
[ ] Package inputs/outputs join coherently across the work chain
[ ] responsible JobRoles are correct
[ ] Flows are associated to the correct current LifecyclePhase
[ ] each Flow has exactly one start Step
[ ] each Step references a Package
[ ] each Step has one or more valid FlowActions
[ ] internal Return loops are intentional
[ ] terminal statuses are intentional
[ ] lifecycle_transition_type is explicitly reviewed
[ ] transition target is specified where required
[ ] domain Endpoint exists if integration is required
[ ] Hooks are defined if integration is required
[ ] HookBindings are attached at correct execution points
[ ] required Hooks are marked required
[ ] optional Hooks are marked optional
[ ] secrets are not present in JSON
[ ] catalog imports successfully
[ ] active Flows pass definition validation
[ ] runtime acceptance test passes
```

---

# 40. Summary

`rimba/kerja` should be administered as a generic work engine, not as the owner of every business domain.

The preferred pattern is:

```text
Domain package
      |
      | lifecycle JSON
      v
Kerja definitions
      |
      v
FlowInstance
      |
      v
Task
      |
      | domain renderer
      v
Domain work
      |
      | FlowAction / Hooks
      v
next Step or terminal outcome
      |
      v
Lifecycle transition
```

For reusable Rimba packages, keep the complete Lifecycle/Flow/Package catalog with the domain package and import it into Kerja. For local procedures, administrators may maintain local definitions separately.

The Document Lifecycle is the first reference implementation for this pattern and should be used as the baseline when building future lifecycle packages.
