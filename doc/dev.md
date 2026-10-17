# Rimba/Kerja High-Level Design

**Status:** Draft High-Level Design  
**Purpose:** Architectural baseline for completing `rimba/kerja`  
**Last updated:** 2026-10-16

---

## 1. Purpose

Rimba/Kerja is an enterprise work orchestration package built around two core ideas:

1. **Workflow** defines **how work is done**, as a procedure or flow.
2. **Package** defines the **assignable bundle of work performed by the same responsible actor**.

Kerja turns these definitions into runtime work through FlowInstances and Tasks.

The package should remain domain-neutral. A Workflow may be used for leave requests, IT service requests, equipment requests, approvals, maintenance work, quality activities, or other business processes without embedding those domain models directly into Kerja.

---

## 2. Design Principles

### 2.1 Definition and runtime are separate

```text
DEFINITION                         RUNTIME

Workflow -----------------------> FlowInstance
   |
   v
FlowStep
   |
   +--> Package ------------> Task
   |
   +--> FlowAction
```

A definition describes what may happen. A runtime instance records what is actually happening.

### 2.2 Workflow owns flow

A Workflow defines the procedure and transitions between Steps.

### 2.3 Package owns work

A Package defines the bundle of work that is assigned together to one responsible actor.

### 2.4 Task is runtime work

A Task is not another work definition. It is the runtime assignment of a Package within a FlowInstance.

### 2.5 Kerja owns workflow data, not domain data

Kerja directly owns its execution state, principally:

- FormData
- Traveller

Domain/application integration is performed through secured REST Endpoints invoked by Hooks.

### 2.6 Configuration over embedded domain logic

Workflow behavior should be declarative where practical. Domain-specific database models, SQL, credentials, arbitrary PHP code, or arbitrary URLs should not be embedded directly in workflow definitions.

---

## 3. Core Workflow Structure

```text
Workflow
|
+-- FlowStep
|   |
|   +-- StepType
|   +-- Package
|   +-- FlowAction
|       |
|       +-- ActionType
|       +-- transition / terminal outcome
|
+-- FlowStep
    ...
```

Every FlowStep must expose at least one valid FlowAction.

Every FlowAction must result in either:

1. transition to another FlowStep; or
2. a terminal FlowInstance outcome.

Reaching a Step does not implicitly complete the Workflow. Completion occurs through an explicit FlowAction.

---

## 4. Step Types

`StepType` is a controlled enum describing the semantic responsibility of a FlowStep.

The initial Kerja grammar contains four StepTypes.

### 4.1 Submit

**Code:** `submit`  
**Label:** Submitting

The requestor provides the required information or work and submits it for processing. The requestor may withdraw the request to stop further processing.

Task action:

```text
Submit
```

### 4.2 Verify

**Code:** `verify`  
**Label:** Verifying

The verifier checks the submitted information or work and confirms that it is verified, meaning correct, complete, and meeting the required criteria. The verifier may also return the request for additional information or correction, or cancel the request.

Task actions:

```text
Verify
Return
Cancel
```

### 4.3 Acknowledge

**Code:** `acknowledge`  
**Label:** Acknowledgement

The acknowledger confirms receipt, awareness, understanding, or concurrence with the submitted information or work by agreeing to proceed. The acknowledger may also return the request for additional information or correction, or cancel the request.

Task actions:

```text
Proceed
Return
Cancel
```

`Acknowledge` is deliberately the StepType while `Proceed` is the positive ActionType.

### 4.4 Approve

**Code:** `approve`  
**Label:** Approval

The approver evaluates the submitted information or work and decides whether it is approved or rejected. The approver may also return the request for additional information or correction.

Task actions:

```text
Approve
Reject
Return
```

### 4.5 StepType action matrix

| StepType | Responsible actor | Allowed Task ActionTypes |
|---|---|---|
| `submit` | Requestor | `submit` |
| `verify` | Verifier | `verify`, `return`, `cancel` |
| `acknowledge` | Acknowledger | `proceed`, `return`, `cancel` |
| `approve` | Approver | `approve`, `reject`, `return` |

`withdraw` is intentionally absent because it is performed by the Requestor against the FlowInstance rather than by the current Tasked Actor against the Task.

---

## 5. Action Types

`ActionType` is the standardized vocabulary of formal acts that may be performed by a person or system.

| Code | Label | Meaning |
|---|---|---|
| `submit` | Submit | Provide the required information or work and submit it for processing. |
| `verify` | Verify | Confirm that the submitted information or work has been checked and is correct, complete, and meets the required criteria. |
| `proceed` | Proceed | Confirm receipt, awareness, understanding, or concurrence and allow processing to proceed. |
| `approve` | Approve | Authorize the submitted request, information, or work to proceed. |
| `reject` | Reject | Decide that the submitted request, information, or work is not approved and should not proceed. |
| `return` | Return | Send the request back because additional information, clarification, correction, or further work is required. |
| `withdraw` | Withdraw | Retract the request and stop further processing at the request of the Requestor. |
| `cancel` | Cancel | Stop further processing because the request or workflow should no longer continue. |

Icons, colors, labels, and descriptions should be defined by the `ActionType` enum rather than stored repeatedly in workflow records.

---

## 6. FlowAction

A `FlowAction` is an `ActionType` configured for a specific FlowStep together with its transition or terminal outcome.

This distinction is fundamental:

```text
ActionType
    WHAT the actor does

FlowAction
    WHAT that action does at THIS FlowStep
```

Example:

```text
FlowStep: Manager Approval

FlowActions
|
+-- approve  --> Fulfilment Step
+-- reject   --> END
+-- return   --> Submission Step
```

An ActionType must not hard-code routing behavior. For example, `approve` may lead to another Step in one Workflow and terminate successfully in another.

---

## 7. Withdraw

`withdraw` is an ActionType but is special in authorization and ownership.

It belongs to the Requestor acting against the active FlowInstance rather than the current Tasked Actor acting against the current Task.

```text
FlowInstance
|
+-- Requestor
|   +-- Withdraw
|
+-- Current Task
    +-- Tasked Actor
        +-- StepType-specific actions
```

This separation must be preserved in authorization logic and UI rendering.

---

## 8. Package

A Package is the complete bundle of work that should be assigned together to the same responsible actor.

```text
FlowStep
    |
    v
Package
    |
    v
responsible actor / role
```

Package describes work; FlowStep describes where that work occurs in the procedure.

Individual instructions within a Package must not be called runtime Tasks. If internal decomposition is required later, a separate term such as `WorkItem` or `ChecklistItem` should be evaluated. It is not part of the current MVP vocabulary.

---

## 9. FlowInstance

A FlowInstance is one running occurrence of a Workflow.

It should hold the runtime context necessary for execution, including at minimum:

```text
FlowInstance
|
+-- workflow reference/version
+-- requestor / initiator
+-- current FlowStep
+-- status
+-- FormData
+-- Traveller
+-- Tasks
```

The exact persistence/versioning implementation remains an implementation-level decision.

---

## 10. Task

A Task is the runtime assignment of a Package within a particular FlowInstance and FlowStep.

```text
Package
    definition of assignable work

Task
    actual runtime assignment of that work
```

A Task records who or what is expected to perform the work and its runtime state.

The Tasked Actor may be a person or a system.

---

## 11. Requestor and Tasked Actor

### Requestor

The person who initiates or owns the request represented by the FlowInstance.

The Requestor normally:

- starts the FlowInstance;
- performs the submitting responsibility where applicable; and
- may withdraw an active request.

### Tasked Actor

The person or system responsible for performing the current Task.

Typical responsibility mapping:

```text
Submitting       -> Requestor
Verifying        -> Verifier
Acknowledgement  -> Acknowledger
Approval         -> Approver
```

How actual actors are resolved or assigned is an implementation concern and is not finalized by this HLD.

---

## 12. FormData

FormData is persistent data entered or produced through workflow forms during execution.

Example:

```json
{
  "leave_type": "annual",
  "start_date": "2026-10-20",
  "end_date": "2026-10-22",
  "days_requested": 3,
  "reason": "..."
}
```

FormData answers:

> What information has been entered or produced through the workflow forms?

---

## 13. Traveller

Traveller is persistent contextual, loaded, and calculated working data carried with a FlowInstance as it moves through the Workflow.

Traveller is **not a cache**. Its persisted value is workflow state and therefore its source of truth belongs with the FlowInstance.

Example:

```json
{
  "leave": {
    "entitlement": {
      "entitled": 20,
      "taken": 5,
      "requested": 3,
      "available": 12
    }
  }
}
```

The semantic separation is:

```text
FormData
    participant-entered or workflow-form-produced data

Traveller
    contextual, loaded, derived, and calculated working data
```

A form may display Traveller data without changing that data's ownership into FormData.

---

## 14. Hooks

A Hook is a uniquely named, reusable, schema-validated data operation invoked at a defined point during workflow execution.

Hooks operate on FormData and Traveller, and may integrate with domain/application data through registered secured REST Endpoints.

Hooks are intended to provide Kerja's data-boundary mechanism without embedding arbitrary domain database access into the workflow engine.

---

## 15. Hook Operations

`HookOperation` is an enum with three operations.

### 15.1 Get

```text
REST Endpoint response
        |
        v
FormData / Traveller
```

A Get Hook retrieves data through an Endpoint and maps response values into FormData and/or Traveller.

### 15.2 Transform

```text
FormData / Traveller
        |
        v
FormData / Traveller
```

A Transform Hook calculates, derives, converts, or rearranges internal workflow data without requiring an Endpoint.

### 15.3 Put

```text
FormData / Traveller
        |
        v
REST Endpoint request
```

A Put Hook maps workflow data into a request sent to an Endpoint.

The resulting invariant is:

```text
GET        REST -> Kerja
TRANSFORM  Kerja -> Kerja
PUT        Kerja -> REST
```

---

## 16. Endpoint

An Endpoint is a registered, secured REST API destination used by Hooks to retrieve or persist data outside the FlowInstance.

Kerja should treat the REST/JSON contract as the domain integration boundary.

This boundary may be used for:

- another application;
- another service;
- an integration API; or
- the hosting application's own domain data exposed through an internal REST API.

Conceptually:

```text
                    Kerja

          +---------------------+
          |                     |
          | FormData Traveller  |
          |     ^       ^       |
          +-----|-------|-------+
                | Hook  |
                v       v
             REST Endpoint
                   |
                   v
             Domain system
```

Endpoint configuration should reference secure authentication configuration rather than embedding credentials inside Hook JSON.

Exact authentication, secret management, network policy, retry, timeout, and API-versioning mechanisms are implementation decisions to be designed separately.

---

## 17. Hook Schema and Configuration

### Hook Schema

Hook Schema is JSON Schema used to validate the permitted structure of a Hook Configuration.

It validates configuration. It does not execute the data operation.

### Hook Configuration

Hook Configuration is the declarative description executed by the Hook engine.

It may describe:

- Endpoint reference;
- HTTP operation/path where applicable;
- request/response paths;
- mappings;
- criteria or parameters;
- converters; and
- internal FormData/Traveller paths.

Conceptually:

```text
JSON Schema
    validates

Hook Configuration
    describes

Hook Runner
    executes
```

---

## 18. Mapping and Paths

A Mapping describes how a value moves from one JSON path to another.

Example:

```json
{
  "from": "response.balance",
  "to": "traveller.leave.balance"
}
```

Another example:

```json
{
  "from": "traveller.leave.balance",
  "to": "request.balance"
}
```

The path root conveys its context, eliminating the need for a separate `Store` concept.

Useful conceptual roots include:

```text
form.*
traveller.*
request.*
response.*
```

The exact path syntax must be finalized as part of the Hook execution design.

---

## 19. Converter

A Converter is a registered value-level operation that may be applied while executing a Mapping.

Examples may include type conversion or safe calculations.

The term `Converter` is deliberately used instead of `Transform` at mapping level so that it is not confused with `HookOperation::Transform`.

Arbitrary executable expressions, SQL, or PHP must not be treated as converters.

The initial converter catalogue remains to be defined.

---

## 20. HookBinding

A Hook defines **what reusable operation exists**.

A HookBinding defines **where, when, and in what sequence it executes**.

```text
Hook
    WHAT operation exists

HookBinding
    WHERE + WHEN it runs
```

A HookBinding may attach a Hook to a Workflow, FlowStep, or FlowAction at an appropriate HookPoint.

---

## 21. Hook Points

The current high-level HookPoints are:

```text
workflow_load
step_enter
step_exit
action_exit
```

### `workflow_load`

Used to prepare initial workflow context when a FlowInstance/form is loaded.

### `step_enter`

Used to prepare data when entering a new FlowStep.

### `step_exit`

Used after the Step's FormData has been validated/saved and before the transition is completed.

### `action_exit`

Used to perform operation(s) associated specifically with the selected FlowAction before transition completion.

Exact transactional sequencing is refined in the runtime execution design section below.

---

## 22. Leave Request Reference Scenario

A Leave Request demonstrates the relationship between FormData, Traveller, Hooks, and Endpoints.

### 22.1 Workflow load

The Requestor starts a Leave Request.

```text
FlowInstance created/loaded
        |
        v
workflow_load Hook
        |
        | GET
        v
Leave Entitlement REST Endpoint
        |
        v
Traveller
```

The Hook loads current entitlement context into Traveller, for example:

```json
{
  "leave": {
    "entitlement": {
      "entitled": 20,
      "taken": 5,
      "available": 15
    }
  }
}
```

### 22.2 Form entry

The Requestor enters FormData such as:

```json
{
  "leave_type": "annual",
  "start_date": "2026-10-20",
  "end_date": "2026-10-22",
  "days_requested": 3
}
```

### 22.3 Step exit

A Transform Hook may use FormData and existing Traveller data to derive updated working values:

```text
FormData + Traveller
        |
        v
Transform Hook
        |
        v
updated Traveller
```

For example, it may derive requested/taken/remaining values.

### 22.4 Action exit

A Put Hook associated with the selected FlowAction may send the required values to a REST Endpoint:

```text
Traveller / FormData
        |
        v
Put Hook
        |
        v
REST Endpoint
```

Whether entitlement should be reserved at Submit, consumed at Approve, released on Reject, or handled differently is a domain workflow decision rather than a behavior hard-coded into `ActionType`.

---

## 23. Hook Definition Persistence

The MVP design uses two definition tables.

### 23.1 `work_hooks`

Purpose: reusable Hook definitions.

Proposed fields:

```text
id
code
name
description
operation
version
schema
configuration
is_active
timestamps
```

Semantics:

| Field | Meaning |
|---|---|
| `code` | Stable unique Hook identifier. |
| `name` | Human-readable name. |
| `description` | Description of the data operation. |
| `operation` | `get`, `transform`, or `put`. |
| `version` | Version of the Hook definition. |
| `schema` | JSON Schema validating configuration. |
| `configuration` | Declarative executable configuration. |
| `is_active` | Whether the Hook may currently be used. |

### 23.2 `work_hook_bindings`

Purpose: attach reusable Hooks to workflow execution points.

Proposed fields:

```text
id
hook_id
hookable_type
hookable_id
hook_point
sequence
parameters
is_required
is_enabled
timestamps
```

Semantics:

| Field | Meaning |
|---|---|
| `hook_id` | Hook being invoked. |
| `hookable_type/id` | Workflow, FlowStep, or FlowAction receiving the binding. |
| `hook_point` | Execution point. |
| `sequence` | Deterministic order when multiple Hooks exist at the same point. |
| `parameters` | Binding-specific configuration values where supported by the Hook schema. |
| `is_required` | Whether Hook failure prevents the workflow operation from continuing. |
| `is_enabled` | Enables/disables the binding without removing it. |

For MVP, `is_required` should normally remain true so that failed required integrations do not silently advance workflow state.

---

## 24. High-Level Runtime Sequence

For an actor performing an action at a FlowStep:

```text
1. Load Task / FlowInstance
        |
2. Present FormData + relevant Traveller context
        |
3. Actor performs Package work
        |
4. Actor selects permitted FlowAction
        |
5. Validate submitted form/action
        |
6. Persist/update FormData
        |
7. Execute StepExit HookBindings in sequence
        |
8. Persist updated Traveller/FormData
        |
9. Execute ActionExit HookBindings in sequence
        |
10. Complete current Task
        |
11. Apply FlowAction transition/outcome
        |
12. Enter next FlowStep or terminate FlowInstance
        |
13. Execute next StepEnter Hooks where applicable
        |
14. Create/activate next Task where applicable
```

The precise transaction model for REST calls requires separate low-level design. Database rollback cannot automatically undo a REST operation that has already committed in another system. Idempotency, compensation, outbox/event patterns, or equivalent reliability mechanisms should therefore be addressed before using Hooks for high-impact cross-system writes.

---

## 25. BusinessObject and Subject

### BusinessObject

Defines the kind of business record that a Workflow concerns.

### Subject

Identifies the specific BusinessObject record associated with a FlowInstance, where applicable.

The Subject must not be used as an excuse for Kerja to directly manipulate domain tables. Domain data integration should follow the Endpoint/Hook boundary.

The precise shape of a Subject reference remains to be finalized.

---

## 26. Glossary

| Term | Definition | Meta |
|---|---|---|
| **Workflow** | Definition of the procedure describing how work flows through connected FlowSteps. | Model / Definition |
| **FlowInstance** | One running execution of a Workflow, carrying runtime state, FormData, Traveller, and Tasks. | Model / Runtime |
| **FlowStep** | Configured stage within a Workflow where a Package is performed and FlowActions determine possible transitions. | Model / Definition |
| **StepType** | Standardized responsibility of a FlowStep: `submit`, `verify`, `acknowledge`, or `approve`. | Enum |
| **ActionType** | Standardized formal act performed by a person/system: `submit`, `verify`, `proceed`, `approve`, `reject`, `return`, `withdraw`, or `cancel`. | Enum |
| **FlowAction** | An ActionType configured for a particular FlowStep together with its transition or terminal outcome. | Model / Definition |
| **Package** | Bundle of work assigned together to the same responsible actor. | Model / Definition |
| **Task** | Runtime assignment of a Package for a particular FlowInstance and FlowStep. | Model / Runtime |
| **Requestor** | Person who initiates or owns the request represented by a FlowInstance and may withdraw it while active. | Runtime Role |
| **Tasked Actor** | Person or system responsible for performing the current Task. | Runtime Role |
| **FormData** | Persistent data entered or produced through workflow forms during execution. | JSON Data / FlowInstance |
| **Traveller** | Persistent contextual, loaded, derived, and calculated working data carried by the FlowInstance. | JSON Data / FlowInstance |
| **Hook** | Uniquely named, reusable, schema-validated data operation that retrieves, transforms, or persists workflow data. | Model / Definition |
| **HookOperation** | Operation performed by a Hook: `get`, `transform`, or `put`. | Enum |
| **HookBinding** | Attaches a Hook to a Workflow, FlowStep, or FlowAction and defines when and in what sequence it executes. | Model / Definition |
| **HookPoint** | Execution point for a HookBinding: currently `workflow_load`, `step_enter`, `step_exit`, or `action_exit`. | Enum |
| **Hook Schema** | JSON Schema defining and validating the permitted structure of Hook Configuration. | JSON Schema |
| **Hook Configuration** | Declarative instructions describing Endpoint requests, mappings, paths, conversions, and execution settings. | JSON Configuration |
| **Endpoint** | Registered secured REST API destination through which Hooks retrieve or persist domain/application data outside the FlowInstance. | Model / Configuration |
| **Mapping** | Declarative rule mapping a value from one JSON path to another during Hook execution. | JSON Configuration |
| **Converter** | Registered safe value-level conversion or derivation used by a Mapping. | Service / Registry |
| **BusinessObject** | Definition of the kind of business record a Workflow concerns. | Model / Definition |
| **Subject** | Reference identifying the specific BusinessObject record associated with a FlowInstance. | Runtime Reference |

---

## 27. Terminology Invariants

The following terminology should be treated as stable unless a concrete use case demonstrates that it cannot represent the required behavior.

### Definition versus runtime

```text
Workflow       -> FlowInstance
Package    -> Task
```

### Controlled vocabulary versus configured definition

```text
StepType       -> FlowStep
ActionType     -> FlowAction
HookOperation  -> Hook
HookPoint      -> HookBinding
```

### Data ownership

```text
FormData
    data entered/produced through workflow forms

Traveller
    persistent workflow working/context data

Endpoint
    REST boundary to domain/application data
```

### Hook direction

```text
GET        REST -> Kerja
TRANSFORM  Kerja -> Kerja
PUT        Kerja -> REST
```

### Acknowledge terminology

```text
StepType     = acknowledge
positive ActionType = proceed
```

There is no separate `acknowledge` ActionType in the current vocabulary.

### Withdraw terminology

`withdraw` remains an ActionType, but it is a Requestor action against the active FlowInstance and is not part of `StepType::allowedActionTypes()`.

---

## 28. Deliberately Deferred Design

This HLD does not yet freeze the following implementation details:

- actor assignment/claiming strategy;
- exact Workflow and Hook versioning mechanics;
- detailed Workflow status enum;
- detailed Task status enum;
- terminal outcome representation;
- dynamic form schema and renderer;
- exact JSON path grammar;
- Converter catalogue;
- Endpoint authentication/secret storage implementation;
- REST retry/idempotency/compensation strategy;
- system actor representation;
- Subject reference format;
- Package internal checklist/work-item representation;
- parallel workflow branches;
- conditional routing;
- timers/escalation/delegation;
- sub-workflows;
- SLA behavior;
- visual workflow designer/BPMN support.

These should not block implementation of the core linear/branching request workflow MVP.

---

## 29. MVP Target

The first usable Kerja implementation should prove one complete vertical path:

```text
Admin defines Packages
        |
Admin defines FlowSteps
        |
Admin assigns StepTypes
        |
Admin configures FlowActions
        |
Admin configures optional Hooks
        |
Requestor starts Workflow
        |
FlowInstance created
        |
FormData / Traveller prepared
        |
Task created for Package
        |
Tasked Actor performs work
        |
Actor selects permitted ActionType
        |
FlowAction Hooks execute
        |
Workflow transitions
        |
Next Task created
        |
...
        |
FlowInstance terminates
```

A reference workflow should exercise at least:

- Submit;
- Verify or Acknowledge;
- Approval;
- Return to an earlier Step;
- Reject or Cancel termination;
- Requestor Withdraw;
- FormData persistence;
- Traveller persistence;
- GET Hook from a REST Endpoint;
- TRANSFORM Hook;
- PUT Hook to a REST Endpoint.

---

## 30. High-Level Definition of Kerja

> **Rimba/Kerja is a domain-neutral enterprise work orchestration package in which a Workflow defines how work flows, a Package defines work assigned together to one actor, and runtime FlowInstances create Tasks that are completed through standardized Actions. Workflow execution carries FormData and Traveller as persistent state, while schema-validated Hooks provide controlled GET, TRANSFORM, and PUT integration with domain systems through secured REST Endpoints.**

This document is the architectural baseline for completing the first usable version of `rimba/kerja`.
