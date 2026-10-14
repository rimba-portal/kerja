# Factory Core Framework — Enterprise Specification

**Package**: `rimba/factory-core`
**Architecture**: PMI PMBOK®-aligned 8-Group Structure · APQC PCF · ISA‑95 · ISO 9001:2026
**Implementation**: Laravel + Filament
**Version**: 2.0 — Restructured
**Date**: 2026‑10‑14

---

## Table of Contents
1. [Purpose & Scope](#1-purpose--scope)
2. [Universal Hierarchy & 7‑Stage Pattern](#2-universal-hierarchy--7-stage-pattern)
3. [Lifecycle Catalogue — 8 Balanced Groups](#3-lifecycle-catalogue--8-balanced-groups)
4. [Framework Data Model](#4-framework-data-model)
5. [Seeding Strategy](#5-seeding-strategy)
6. [Package Structure](#6-package-structure)
7. [Compliance Alignment](#7-compliance-alignment)
8. [Implementation Principles](#8-implementation-principles)

---

## 1. Purpose & Scope

This document defines the unified operating model for the entire manufacturing enterprise. It establishes:
- All enterprise lifecycles organised into **8 clear, balanced groups**
- Universal 7‑stage pattern applied consistently across every lifecycle
- Workflow → Node → WorkPackage → Task execution hierarchy
- Database structure & relationships
- Seeding file organisation and naming conventions
- Compliance alignment with APQC PCF, ISA‑95, and ISO 9001:2026

This specification governs the `rimba/factory-core` Laravel + Filament package. All modules and extensions must conform to the structures defined herein.

---

## 2. Universal Hierarchy & 7‑Stage Pattern

### 2.1 Execution Hierarchy

LIFECYCLE → STAGE → WORKFLOW → NODE → WORK PACKAGE → TASK
plaintext

Every lifecycle follows this identical structure — see Version 1.0 for full layer definitions.

### 2.2 The 7 Universal Stages
| # | Code | Name | Core Action |
|---|---|---|---|
| 1 | `DEFINE` | Define | Author, design, plan, specify |
| 2 | `QUALIFY` | Qualify | Validate, pilot, test, approve |
| 3 | `ACTIVATE` | Activate | Release, deploy, onboard, go‑live |
| 4 | `EXECUTE` | Execute | Run, produce, transact, deliver |
| 5 | `MONITOR` | Monitor | Measure, track, trend, alert |
| 6 | `MAINTAIN` | Maintain | Renew, repair, recertify, improve |
| 7 | `RETIRE` | Retire | Phase out, decommission, archive |

All transitions are immutable and logged to `lifecycle_transitions`.

---

## 3. Lifecycle Catalogue — 8 Balanced Groups

### Group 1 — 🎯 Integration, Strategy & Governance
*Overall direction, accountability, and organisational standards*

| Master Lifecycle | Original Items Covered | Seeder File |
|---|---|---|
| Strategy & Direction | Strategy Lifecycle | `SeedStrategyWorkflows.php` |
| Project & Initiative Management | Project Lifecycle | `SeedProjectWorkflows.php` |
| Risk Management | Risk Lifecycle | `SeedRiskWorkflows.php` |
| Compliance & Standards | Compliance Lifecycle | `SeedComplianceWorkflows.php` |
| Audit & Verification | Audit Lifecycle | `SeedAuditWorkflows.php` |
| Continuous Improvement | Continuous Improvement Lifecycle | `SeedContinuousImprovementWorkflows.php` |

---

### Group 2 — 👤 People, Talent & Stakeholders
*Your workforce and key external relationships*

| Master Lifecycle | Original Items Covered | Seeder File |
|---|---|---|
| Workforce & Employee Journey | Workforce, Employee | `SeedWorkforceWorkflows.php` |
| Talent Acquisition | Recruitment | `SeedRecruitmentWorkflows.php` |
| Onboarding & Integration | Onboarding | `SeedOnboardingWorkflows.php` |
| Learning & Certification | Training, Certification | `SeedTrainingCertificationWorkflows.php` |
| Performance & Career Growth | Performance, Career Development | `SeedPerformanceCareerWorkflows.php` |
| Succession Planning | Succession | `SeedSuccessionWorkflows.php` |
| Offboarding & Transition | Offboarding | `SeedOffboardingWorkflows.php` |
| Customer Engagement | Customer | `SeedCustomerWorkflows.php` |
| Partner & Contractor Relations | Partner, Contractor | `SeedPartnerContractorWorkflows.php` |

---

### Group 3 — 📦 Product, Design & Quality
*From concept through assurance*

| Master Lifecycle | Original Items Covered | Seeder File |
|---|---|---|
| Product & Portfolio Management | Product, Market, Service | `SeedProductPortfolioWorkflows.php` |
| New Product Introduction | NPI | `SeedNPIWorkflows.php` |
| Design & Engineering | Product Design, Engineering Change | `SeedDesignEngineeringWorkflows.php` |
| Qualification & Validation | Product Qualification | `SeedQualificationValidationWorkflows.php` |
| Quality Management | Quality, Incident | `SeedQualityWorkflows.php` |
| CAPA & Corrective Action | CAPA | `SeedCAPAWorkflows.php` |

---

### Group 4 — ⚙️ Production, Processes & Execution
*Making and delivering output*

| Master Lifecycle | Original Items Covered | Seeder File |
|---|---|---|
| Demand & Fulfilment | Demand, Sales, Order | `SeedDemandFulfilmentWorkflows.php` |
| Production & Manufacturing | Production, Manufacturing Process | `SeedProductionWorkflows.php` |
| Work Order Execution | Work Order | `SeedWorkOrderWorkflows.php` |

---

### Group 5 — 🏗️ Assets, Facilities & Infrastructure
*Physical resources enabling work*

| Master Lifecycle | Original Items Covered | Seeder File |
|---|---|---|
| Asset & Technology Management | Asset, Technology | `SeedAssetTechnologyWorkflows.php` |
| Equipment Management | Equipment | `SeedEquipmentWorkflows.php` |
| Maintenance & Reliability | Maintenance | `SeedMaintenanceWorkflows.php` |
| Tooling & Metrology | Tooling, Calibration | `SeedToolingCalibrationWorkflows.php` |
| Facility Management | Facility | `SeedFacilityWorkflows.php` |

---

### Group 6 — 🚚 Supply Chain, Materials & Logistics
*Sourcing, stock, and movement*

| Master Lifecycle | Original Items Covered | Seeder File |
|---|---|---|
| Supplier & Vendor Management | Supplier, Vendor | `SeedSupplierVendorWorkflows.php` |
| Supply Chain & Logistics | Supply Chain, Logistics | `SeedSupplyChainLogisticsWorkflows.php` |
| Materials Management | Material | `SeedMaterialWorkflows.php` |
| Inventory Control | Inventory | `SeedInventoryWorkflows.php` |

---

### Group 7 — 🌱 Environment & Sustainability
*Responsible operations*

| Master Lifecycle | Original Items Covered | Seeder File |
|---|---|---|
| Environmental Management | Environmental | `SeedEnvironmentalWorkflows.php` |
| Sustainability Stewardship | Sustainability | `SeedSustainabilityWorkflows.php` |

---

### Group 8 — 📑 Documents, Knowledge & Records
*Organisational memory*

| Master Lifecycle | Original Items Covered | Seeder File |
|---|---|---|
| Document Control & Records | Document | `SeedDocumentWorkflows.php` |
| Knowledge Management | Knowledge | `SeedKnowledgeWorkflows.php` |

---

## 4. Framework Data Model

Core tables unchanged from Version 1.0:
- `lifecycles` — master list; grouped via `group_code` field
- `lifecycle_stages` — fixed 7 stages per lifecycle
- `workflow_definitions` — stage‑level templates
- `workflow_instances` — running instances on entities
- `workflow_nodes` — steps, roles, conditions
- `work_packages` — assignable batches
- `package_tasks` — atomic items
- `lifecycle_transitions` — immutable audit trail

New field: `lifecycles.group_code` → `INTEGRATION`, `PEOPLE`, `PRODUCT`, `PRODUCTION`, `ASSETS`, `SUPPLY`, `ENVIRONMENT`, `KNOWLEDGE`

---

## 5. Seeding Strategy

- **One seeder per master lifecycle** → creates all 7 stage workflows + nodes + role assignments
- **Namespace by group** → clean autoloading in Laravel
- **`SeedLifecycles.php`** → master seeder; calls all group sub‑seeders
- **Baseline = compliant** → every installation gets identical ISO‑aligned starting data
- **Customise via Filament** → change definitions without touching seeders

---

## 6. Package Structure

---

## 7. Compliance Alignment

| Standard | Coverage |
|---|---|
| **PMI PMBOK®** | Groupings align with Integration, Resource/Stakeholder, Quality, Procurement/Supply domains |
| **APQC PCF** | Full coverage of all core categories |
| **ISA‑95** | Level 3 execution + Level 4 enterprise integration |
| **ISO 9001:2026** | PDCA embedded in 7‑stage pattern; immutable evidence in `lifecycle_transitions` |

---

## 8. Implementation Principles

1. **One Engine — All Lifecycles**: `HasLifecycle` + `WorkflowRunner` reused everywhere
2. **Data Driven**: New workflows = new rows, no code changes
3. **Grouped for Clarity**: 8 balanced groups — neither too broad nor too granular
4. **Audit by Default**: Every transition logged; no true deletions
5. **Filament Native**: Resources, dashboards, actions built for Filament panels
6. **Version Controlled**: Seeders and spec committed to repo; SemVer followed

---

*End of Specification — Governs `rimba/factory-core`*
