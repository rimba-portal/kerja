
# Factory Core Framework — Enterprise Specification

**Package**: `rimba/factory-core`
**Architecture**: APQC PCF · ISA‑95 · ISO 9001:2026
**Implementation**: Laravel + Filament
**Version**: 1.0 — Final
**Date**: 2026‑10‑14

---

## Table of Contents
1. [Purpose & Scope](#1-purpose--scope)
2. [Universal Hierarchy Definition](#2-universal-hierarchy-definition)
3. [Complete Lifecycle Inventory](#3-complete-lifecycle-inventory)
4. [Framework Data Model](#4-framework-data-model)
5. [Seeding Specification — Per Lifecycle](#5-seeding-specification--per-lifecycle)
6. [Package Structure](#6-package-structure)
7. [Compliance Alignment](#7-compliance-alignment)
8. [Implementation Principles](#8-implementation-principles)

---

## 1. Purpose & Scope

This document defines the unified operating model for the entire manufacturing enterprise. It establishes:
- All enterprise lifecycles (domains) covering operations, supply chain, and governance
- Universal 7‑stage pattern applied consistently across every domain
- Workflow → Node → WorkPackage → Task execution hierarchy
- Database structure & relationships
- Compliance alignment with APQC PCF, ISA‑95, and ISO 9001:2026
- Baseline seeding requirements per lifecycle

This specification governs the `rimba/factory-core` Laravel + Filament package. All modules and extensions must conform to the structures defined herein.

---

## 2. Universal Hierarchy Definition

LIFECYCLE (Domain Entity)
└── STAGE (Fixed sequence: 7 phases)
└── WORKFLOW (Procedure within the stage)
└── NODE / STEP (Action or decision point)
└── WORK PACKAGE (Assignable group of tasks → Role)
└── TASK (Atomic action → Individual user)

### 2.1 Layer Definitions

| Layer | Definition |
|---|---|
| **Lifecycle** | End‑to‑end journey of a major enterprise entity |
| **Stage** | Fixed sequential phase; defines status, permissions, and transition rules |
| **Workflow** | Configurable process template to advance through or complete a stage |
| **Node / Step** | Discrete action, review, approval, or decision gate in the workflow |
| **Work Package** | Batched set of tasks; assigned to a specific role; has due date & progress tracking |
| **Task** | Smallest unit of actionable work; completion drives node → workflow → stage progression |

### 2.2 Universal 7‑Stage Pattern

**Applies identically to ALL lifecycles — no exceptions**

| # | Stage Code | Stage Name | Status When Active | Primary Action |
|---|---|---|---|---|
| 1 | `DEFINE` | Define | Draft / Specified | Author, design, outline, consult, internal review |
| 2 | `QUALIFY` | Qualify | Qualified / Validated | Pilot, verify, validate, inspect, formal approval |
| 3 | `ACTIVATE` | Activate | Active / In‑Service | Deploy, announce, train, grant access, formal release |
| 4 | `EXECUTE` | Execute | In‑Operation | Run, produce, record, transform, deliver, interact |
| 5 | `MONITOR` | Monitor | Under‑Observation | Collect data, analyse, compare to target, alert, report |
| 6 | `MAINTAIN` | Maintain | Sustained / Refreshed | Recertify, repair, update, retrain, improve, restock |
| 7 | `RETIRE` | Retire | Archived / Decommissioned | Suspend, revoke access, archive, dispose, document lessons learned |

**Transition Rules**:
- Stages are sequential; skipping requires explicit system override permission
- Every transition logged to `lifecycle_transitions` table — immutable audit trail
- Global status model: `DRAFT → ACTIVE → RESTRICTED → OBSOLETE → ARCHIVED`

**Reminder Triggers** (applies to all expirable entities):
- 30 days before deadline → First reminder
- 15 days before deadline → Second reminder
- 7 days before deadline → Final warning + supervisor escalation
- On expiry → Status restricted; access suspended
- Grace period → 5 days (configurable); mandatory remediation after

---

## 3. Complete Lifecycle Inventory

### Group A — Core Operations (ISA‑95 Level 3 — Manufacturing Execution)

| ID | Lifecycle Code | Lifecycle Name | Primary Entities |
|---|---|---|---|
| O‑01 | `PEOPLE` | People & Competency | Person, Role, Competency, Certification |
| O‑02 | `PROCESS` | Process & Standard Work | Process, SOP, Routing, ControlPlan |
| O‑03 | `EQUIPMENT` | Equipment & Assets | Equipment, Asset, MaintenancePlan, Calibration |
| O‑04 | `MATERIAL` | Materials & Inventory | Material, Lot, Location, BillOfMaterial |
| O‑05 | `COMMS` | Comms & Instructions | Message, Broadcast, Channel, Template |
| O‑06 | `PRODUCT` | Product & Recipe | ProductDefinition, Recipe, Specification |
| O‑07 | `WORKORDER` | Work Order & Execution | WorkOrder, Schedule, Allocation, ResponseLog |
| O‑08 | `QUALITY` | Quality & Conformance | QualityCheck, Nonconformance, CAPA, Certificate |

### Group B — Supply & Enterprise Integration (ISA‑95 Level 4 / APQC)

| ID | Lifecycle Code | Lifecycle Name | Primary Entities |
|---|---|---|---|
| S‑01 | `SUPPLIER` | Procurement & Supplier Management | Supplier, PurchaseOrder, Shipment, VendorRating |
| S‑02 | `DOCUMENT` | Document & Knowledge Management | Document, Revision, Approval, Record |
| S‑03 | `COMPLIANCE` | Compliance, Risk & Audit | ComplianceStandard, RiskItem, AuditPlan, Finding |
| S‑04 | `COST` | Cost & Resource Management | CostCenter, Budget, Allocation, Expense |
| S‑05 | `STRATEGY` | Strategy & Organisational Capability | StrategicGoal, Initiative, Capability, Investment |

**Total**: 13 lifecycles — all powered by the same engine

---

## 4. Framework Data Model

### Core Tables

| Table Name | Purpose | Key Fields |
|---|---|---|
| `lifecycles` | Master list of all domains | `code`, `name`, `description`, `entity_model` |
| `lifecycle_stages` | Fixed 7 stages per lifecycle | `lifecycle_id`, `stage_code`, `sequence`, `default_status` |
| `workflow_definitions` | Reusable process templates per stage | `lifecycle_id`, `stage_code`, `name`, `trigger_type`, `policy_set` |
| `workflow_instances` | Active running workflow on a specific entity | `definition_id`, `entity_type`, `entity_id`, `current_node_index`, `status` |
| `workflow_nodes` | Steps within a definition | `workflow_definition_id`, `node_type`, `sequence`, `target_role_code`, `condition` |
| `work_packages` | Assignable task batch | `workflow_instance_id`, `node_id`, `due_date`, `status` |
| `package_tasks` | Atomic items inside a package | `package_id`, `task_type`, `label`, `action_config`, `completed_at`, `actor_id` |
| `lifecycle_transitions` | Universal immutable audit trail — ONE table for EVERYTHING | `entity_type`, `entity_id`, `from_stage`, `to_stage`, `actor_id`, `evidence_url`, `occurred_at` |

### Supporting Tables

| Table Name | Purpose |
|---|---|
| `roles` | Assignable role definitions |
| `role_user` | User ↔ Role assignments |
| `reminder_rules` | 30/15/7‑day trigger configuration per entity type |
| `entity_relationships` | ISA‑95 cross‑links: WorkOrder → Product/People/Equipment/Material |

### Key Enums

| Enum | Values |
|---|---|
| `LifecycleStage` | `DEFINE, QUALIFY, ACTIVATE, EXECUTE, MONITOR, MAINTAIN, RETIRE` |
| `Status` | `DRAFT, ACTIVE, RESTRICTED, OBSOLETE, ARCHIVED` |
| `NodeType` | `INPUT, REVIEW, VERIFY, AUTHORISE, NOTIFY, GATE` |
| `PackageStatus` | `PENDING, IN_PROGRESS, SUBMITTED, APPROVED, OVERDUE, CLOSED` |
| `TaskType` | `FORM, UPLOAD, QUIZ, OBSERVATION, ACKNOWLEDGE` |

### Traits — Shared Across All Entities

| Trait | Purpose |
|---|---|
| `HasLifecycle` | Stage management: `advanceTo()`, `canTransitionTo()`, `isActive()` |
| `HasWorkflows` | Process execution: `startWorkflow()`, `currentPackage()`, `progress()` |
| `HasAudit` | Immutable history: `created_by`, `updated_by`, timestamps |
| `HasExpiry` | Deadline handling: `due_date`, `isExpired()`, `daysUntilReminder()` |

---

## 5. Seeding Specification — Per Lifecycle

**Naming Convention**: `Seed{LifecycleName}DefaultWorkflows`
**Location**: `src/Database/seeders/`
**Rule**: Each seeder populates stage‑level workflow definitions, node sequences, role assignments, and default due‑date policies.

---

### O‑01 — People & Competency

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | Role & Competency Profile | 1. Define requirements → HR<br>2. Internal review → Dept Head<br>3. Final approval → Manager |
| **QUALIFY** | Training & Certification | 1. Complete learning modules → Operator<br>2. Knowledge check → Operator<br>3. Practical observation → Assessor<br>4. Certification grant → Supervisor |
| **ACTIVATE** | Onboarding & Deployment | 1. System access provisioning → IT<br>2. Roster assignment → Shift Lead<br>3. Orientation & handover → Trainer |
| **EXECUTE** | Daily Task Execution | 1. Perform assigned work → Operator<br>2. Record evidence → Operator<br>3. Shift handover → Operator |
| **MONITOR** | Expiry & Gap Review | 1. Auto‑flag expiring certs → System<br>2. Gap analysis → Supervisor<br>3. Cross‑training plan → Manager |
| **MAINTAIN** | Recertification | 1. Refresh training → Operator<br>2. Re‑assessment → Assessor<br>3. Renew certification → Supervisor |
| **RETIRE** | Offboarding | 1. Exit interview → HR<br>2. Revoke all access → IT<br>3. Knowledge transfer → Peer<br>4. Archive record → HR |

---

### O‑02 — Process & Standard Work

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | SOP Authoring | 1. Draft → Process Owner<br>2. Technical review → Peer<br>3. Format & numbering → Doc Control |
| **QUALIFY** | Validation & Approval | 1. Pilot run → Production<br>2. Safety review → HSE<br>3. Final approval → Dept Head |
| **ACTIVATE** | Release & Training | 1. Distribute → Doc Control<br>2. Acknowledge receipt → All Affected<br>3. Confirm understanding → Supervisor |
| **EXECUTE** | Work Order Execution | 1. Follow SOP → Operator<br>2. Record parameters → Operator<br>3. Deviation report → Operator |
| **MONITOR** | Adherence Audit | 1. Observe practice → QA/Lead<br>2. Trend non‑conformances → QA<br>3. Feedback collection → Process Owner |
| **MAINTAIN** | Revision & Improvement | 1. Propose change → Any User<br>2. Full re‑approval → Same chain<br>3. Supersede → Doc Control |
| **RETIRE** | Archive & Replace | 1. Mark obsolete → Doc Control<br>2. Link replacement → Doc Control<br>3. Purge per retention → Doc Control |

---

### O‑03 — Equipment & Assets

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | Spec & Acquisition Planning | 1. Define technical specs → Engineering<br>2. Budget request → Finance<br>3. Layout & utilities → Facilities |
| **QUALIFY** | IQ/OQ/PQ Validation | 1. Installation verification → Vendor/Engineer<br>2. Operational qualification → QA/Engineer<br>3. Performance qualification → Production/Engineering<br>4. Final sign‑off → Plant Manager |
| **ACTIVATE** | Handover to Production | 1. Spare parts stock → Maintenance<br>2. Operator training → Trainer<br>3. Logbook opened → Maintenance Lead |
| **EXECUTE** | Production Run Cycle | 1. Load recipe → Machine Operator<br>2. Log readings & cycles → Operator<br>3. Report faults → Operator |
| **MONITOR** | Condition & Trend Review | 1. Review metrics → Maintenance Tech<br>2. Flag anomalies → System/Maintenance<br>3. Investigate root cause → Engineer |
| **MAINTAIN** | Preventive Maintenance | 1. Pre‑service check → Tech<br>2. Perform service → Tech<br>3. Calibration verification → QA<br>4. Close work order → Supervisor |
| **RETIRE** | Decommissioning | 1. Isolation & LOTO → Maintenance<br>2. Asset removal → Facilities<br>3. Financial write‑off → Finance<br>4. Archive history → Maintenance Lead |

---

### O‑04 — Materials & Inventory

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | Part Master Creation | 1. Spec & tolerances → Quality<br>2. Storage/shelf‑life rules → Warehouse<br>3. BOM linkage → Engineering |
| **QUALIFY** | Incoming Inspection | 1. Verify COA matches → QC<br>2. Sample testing → QC<br>3. Accept/reject decision → Quality Manager |
| **ACTIVATE** | Put‑away & Labelling | 1. Assign bin location → Warehouse<br>2. Print & apply tags → Warehouse<br>3. Update inventory system → Inv Clerk |
| **EXECUTE** | Issue & Consumption | 1. Pick & confirm → Warehouse<br>2. Load to work order → Production<br>3. Log usage → Production |
| **MONITOR** | Stock & Expiry Control | 1. FIFO compliance → System/Warehouse<br>2. Low‑stock alert → Buyer<br>3. Shelf‑life countdown → Inv Control |
| **MAINTAIN** | Cycle Count & Retest | 1. Physical count → Inv Control<br>2. Reconcile variances → Finance<br>3. Retest borderline lots → QC<br>4. Regrade if needed → Quality |
| **RETIRE** | Scrap / Return to Vendor | 1. Quarantine → Warehouse<br>2. Disposal approval → Quality<br>3. Execute disposal/RTV → Warehouse<br>4. Financial adjustment → Finance |

---

### O‑05 — Comms & Instructions

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | Template & Channel Design | 1. Draft content → Comms Owner<br>2. Define audience → Process Lead<br>3. Approval chain → Manager |
| **QUALIFY** | Pilot & Clarity Check | 1. Test broadcast → Comms<br>2. Confirm understanding → Sample Group<br>3. Refine → Comms |
| **ACTIVATE** | Go‑Live & Notify | 1. Open channel → Comms<br>2. First broadcast → Comms<br>3. Confirm receipt → All |
| **EXECUTE** | Daily Communications | 1. Send/receive → All<br>2. Acknowledge → Recipient<br>3. Escalate unread → System |
| **MONITOR** | Read Rate & Feedback | 1. Report compliance → Comms<br>2. Sentiment/feedback → Comms<br>3. Retrain low‑compliance → Supervisor |
| **MAINTAIN** | Refresh & Update | 1. Archive old → Comms<br>2. Update template → Comms<br>3. Redistribute → Comms |
| **RETIRE** | Close & Archive | 1. Close channel → Comms<br>2. Preserve logs → Comms<br>3. Document lessons → Comms |

---

### O‑06 — Product & Recipe

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | Design & Specification | 1. Define formula → R&D/Eng<br>2. BOM structure → Eng<br>3. Quality targets → QA |
| **QUALIFY** | Pilot & Validation | 1. Trial production → Prod/Eng<br>2. Yield analysis → QA<br>3. Sign‑off → Product Mgr |
| **ACTIVATE** | Release to Manufacturing | 1. Effective date → Eng<br>2. Train affected → Prod Lead<br>3. Go‑live notification → Production |
| **EXECUTE** | Production Runs | 1. Load recipe → Operator<br>2. Record results → Operator<br>3. First‑off check → QC |
| **MONITOR** | Yield & Variation | 1. Trend yields → QA<br>2. Stability analysis → Eng<br>3. Drift alerts → System |
| **MAINTAIN** | Version Update | 1. Propose change → Eng<br>2. Re‑validate → QA<br>3. Supersede → Eng |
| **RETIRE** | End‑of‑Life | 1. Announce EOL → Sales<br>2. Disposition stock → Inv Control<br>3. Archive recipe → Eng |

---

### O‑07 — Work Order & Execution

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | Create & Schedule | 1. Generate → Planner<br>2. Check material availability → Inv<br>3. Schedule → Prod Scheduler |
| **QUALIFY** | Allocation Check | 1. Verify personnel → Shift Lead<br>2. Verify equipment → Maint<br>3. Confirm release → Prod Mgr |
| **ACTIVATE** | Release to Floor | 1. Print/notify → Prod Lead<br>2. Allocate materials → Warehouse<br>3. Start timer → Operator |
| **EXECUTE** | Run & Record | 1. Load → Operator<br>2. Log data → Operator<br>3. Pause/report issues → Operator |
| **MONITOR** | Track & Flag | 1. Status update → Prod Lead<br>2. Alert delay → System<br>3. Escalate → Prod Mgr |
| **MAINTAIN** | Analyse & Improve | 1. Review variances → Prod Lead<br>2. Update standards → Eng<br>3. Feedback loop → All |
| **RETIRE** | Close & Archive | 1. Final confirmation → QC<br>2. Cost roll‑up → Finance<br>3. Archive → Prod Lead |

---

### O‑08 — Quality & Conformance

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | Standard & Inspection Method | 1. Draft procedure → QA<br>2. Cross‑functional review → Production<br>3. Approve → Quality Manager |
| **QUALIFY** | Method Validation | 1. Run trials → QC<br>2. Correlate data → Engineer<br>3. Confirm control limits → QA |
| **ACTIVATE** | Deploy to Production Line | 1. Train inspectors → QA<br>2. Go‑live → Production Lead<br>3. Monitor first 3 lots → QA |
| **EXECUTE** | Routine Inspection | 1. Perform check → Inspector<br>2. Record results → Inspector<br>3. Flag OOC → System/QA |
| **MONITOR** | Trend & SPC Review | 1. Daily chart review → QA<br>2. Capability analysis → Engineer<br>3. Customer feedback loop → QA Manager |
| **MAINTAIN** | CAPA & Improvement | 1. Containment → Production Lead<br>2. Root‑cause analysis → Cross‑functional Team<br>3. Implement fix → Process Owner<br>4. Verify effectiveness → QA |
| **RETIRE** | Obsolete Standard Withdrawal | 1. Withdraw all copies → QA<br>2. Archive → Doc Control<br>3. Communicate → All Affected |

---

### S‑01 — Procurement & Supplier

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | Supplier Registration | 1. Collect profile → Buyer<br>2. Legal review → Procurement<br>3. Approval → Procurement Mgr |
| **QUALIFY** | Evaluation & Approval | 1. Audit/assessment → QA/Buyer<br>2. Sample test → QC<br>3. Add to AVL → Procurement |
| **ACTIVATE** | First Order | 1. Create PO → Buyer<br>2. Confirm terms → Procurement<br>3. Release → Procurement |
| **EXECUTE** | Receive & Pay | 1. Receipt → Warehouse<br>2. Invoice match → Finance<br>3. Payment → Finance |
| **MONITOR** | Performance Rating | 1. On‑time → System<br>2. Quality score → QA<br>3. Review → Buyer |
| **MAINTAIN** | Review & Renegotiate | 1. Annual review → Procurement<br>2. Contract update → Legal<br>3. Re‑approve → Procurement |
| **RETIRE** | Deactivate | 1. Flag inactive → Buyer<br>2. Final settlement → Finance<br>3. Archive → Procurement |

---

### S‑02 — Document & Knowledge

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | Authoring | 1. Draft → Author<br>2. Technical review → Peer<br>3. Format → Doc Control |
| **QUALIFY** | Approval Cycle | 1. Dept review → Manager<br>2. Cross‑review → Affected<br>3. Final sign‑off → Owner |
| **ACTIVATE** | Distribute | 1. Publish → Doc Control<br>2. Acknowledge → All<br>3. Confirm → Supervisor |
| **EXECUTE** | Reference & Feedback | 1. Access → All<br>2. Clarify question → User<br>3. Respond → Author |
| **MONITOR** | Effectiveness Review | 1. Usage metrics → System<br>2. Feedback analysis → Doc Control<br>3. Gap identification → Owner |
| **MAINTAIN** | Revise | 1. Propose change → Any<br>2. Re‑approval → Same chain<br>3. Supersede → Doc Control |
| **RETIRE** | Archive | 1. Mark obsolete → Doc Control<br>2. Retention hold → System<br>3. Purge → Doc Control |

---

### S‑03 — Compliance, Risk & Audit

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | Register & Assess | 1. Identify item → Process Owner<br>2. Score severity → Risk Mgr<br>3. Approve rating → Manager |
| **QUALIFY** | Plan & Validate | 1. Mitigation plan → Owner<br>2. Resource check → Finance<br>3. Approve plan → Risk Mgr |
| **ACTIVATE** | Implement | 1. Communicate → Owner<br>2. Train affected → Supervisor<br>3. Go‑live → Owner |
| **EXECUTE** | Monitor Controls | 1. Perform check → Assigned<br>2. Log evidence → Assigned<br>3. Report → Owner |
| **MONITOR** | Review Effectiveness | 1. Trend → Risk Mgr<br>2. Audit check → Internal Audit<br>3. Escalate gaps → Risk Mgr |
| **MAINTAIN** | Update & Close | 1. Reassess → Owner<br>2. Adjust plan → Risk Mgr<br>3. Close → Manager |
| **RETIRE** | Archive Finding | 1. Close → Auditor<br>2. Retain evidence → System<br>3. Document lesson → Risk Mgr |

---

### S‑04 — Cost & Resource

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | Budget & Allocation | 1. Draft → Cost Center Lead<br>2. Review → Finance<br>3. Approve → Mgmt |
| **QUALIFY** | Validate & Commit | 1. Check vs forecast → Finance<br>2. Commit funds → Finance<br>3. Notify → Lead |
| **ACTIVATE** | Go‑Live & Track | 1. Open codes → Finance<br>2. Communicate → Lead<br>3. First review → Finance |
| **EXECUTE** | Record & Match | 1. Code expense → Requestor<br>2. Approve → Lead<br>3. Post → Finance |
| **MONITOR** | Variance Analysis | 1. Flag deviation → Finance<br>2. Investigate → Lead<br>3. Report → Finance |
| **MAINTAIN** | Forecast Adjust | 1. Propose → Lead<br>2. Re‑forecast → Finance<br>3. Approve → Mgmt |
| **RETIRE** | Close Period | 1. Final accrual → Finance<br>2. Lock → Finance<br>3. Archive → Finance |

---

### S‑05 — Strategy & Capability

| Stage | Workflow Name | Nodes / Assigned Roles |
|---|---|---|
| **DEFINE** | Goal & Initiative | 1. Propose → Leader<br>2. Feasibility → Cross‑team<br>3. Approve → Exec |
| **QUALIFY** | Plan & Resource | 1. Detail plan → Owner<br>2. Budget → Finance<br>3. Launch sign‑off → Sponsor |
| **ACTIVATE** | Kickoff | 1. Announce → Sponsor<br>2. Team onboard → Owner<br>3. First milestone → Owner |
| **EXECUTE** | Deliver | 1. Execute tasks → Team<br>2. Update status → Owner<br>3. Checkpoint → Sponsor |
| **MONITOR** | Track & Report | 1. KPI review → Owner<br>2. Gate review → Sponsor<br>3. Public update → Comms |
| **MAINTAIN** | Pivot & Improve | 1. Retrospective → Team<br>2. Adjust plan → Owner<br>3. Re‑align → Sponsor |
| **RETIRE** | Conclude | 1. Final report → Owner<br>2. Celebrate → Sponsor<br>3. Institutionalise → All |

---

## 6. Package Structure


---

## 7. Compliance Alignment

| Standard / Framework | Coverage | How It Maps |
|---|---|---|
| **APQC PCF** | Full — 13 lifecycles match all core categories | Operations, supply chain, HR, IT/docs, finance, risk, strategy |
| **ISA‑95** | Level 3 (execution) + Level 4 (enterprise integration) | Standard object models: Personnel, Equipment, Material, Product, WorkOrder; cross‑link relationships |
| **ISO 9001:2026** | All 10 clauses — natively embedded | Define/Qualify=Plan → Activate/Execute=Do → Monitor=Check → Maintain/Retire=Act; every clause produces timestamped, signed evidence in `lifecycle_transitions` |

**Auditor Evidence Promise**: Any question from any standard is answered from the same immutable audit table. No separate spreadsheets. No siloed records.

---

## 8. Implementation Principles

1. **Engine Once — Apply Everywhere**
   - `HasLifecycle` trait and `WorkflowRunner` service written once
   - All 13 lifecycles reuse identical logic
   - New domain = new rows + seeding; zero new lifecycle engine code

2. **Data‑Driven Configuration**
   - Workflows defined in database via seeders or Filament UI
   - Business rules change without touching code
   - Baseline seeders provide compliant starting point; customise per site

3. **Audit by Default**
   - Every stage change → immutable log entry
   - Who → What → When → Evidence → All preserved
   - Soft‑delete only; true record never erased

4. **Filament as Configuration & Operations UI**
   - Resources = define and manage structures
   - Dashboard = operational oversight
   - No business logic in UI layer — all in Services/Traits

5. **Relationship First**
   - ISA‑95 genealogy built in: trace from finished good → raw material → operator → machine → instruction
   - `ReadinessService` validates all domains in one call before production release

6. **Version Control Friendly**
   - This specification = living document
   - Seeders = baseline data committed to repo
   - Schema migrations = versioned changes
   - Package follows SemVer

---

*End of Specification — Governs `rimba/factory-core`*