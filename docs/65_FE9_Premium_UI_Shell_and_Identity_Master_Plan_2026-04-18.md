# FE9 Premium UI Shell and Identity Master Plan

**Date:** 2026-04-18  
**Status:** Approved master plan  
**Theme Direction:** Precision Ops Control Room  
**Execution Standard:** No Quick & Dirty, no hotfix masking, no trial-and-error styling

## Why FE9 Exists

The previous frontend waves improved internal screen quality, but they did not fully solve the system-level perception problem.

The real issues are:

1. app shell composition is structurally wrong
2. auth and welcome surfaces are underpowered as product entry experiences
3. system-level identity is not yet strong enough across every screen family

This plan exists to fix the problem in the correct order:

- structure first
- shared layout second
- identity third
- polish and QA last

## What FE9 Is Not

FE9 will **not** use:

- CSS nudges to fake a left rail
- isolated page repainting without shell ownership repair
- dark mode expansion
- template copying from generic admin kits
- random visual experimentation without a governing system

## FE9 Phase Map

### FE9-A App Shell Architecture Repair

Goal:

- restore the authenticated shell so the left rail becomes the real product frame

Scope:

- make `flux:sidebar`, `flux:header`, and `flux:main` direct siblings under one shell parent
- move `flux:main` ownership into the shell layout
- remove nested `flux:main` from the inner layout
- validate skip-link, mobile toggle, and authenticated screen rendering

Success criteria:

- sidebar occupies the left shell as intended
- content aligns beside the rail, not below a detached block
- dashboard, incidents, settings, templates, and staff flows all inherit the repaired frame

Priority:

- mandatory
- must land before any visual identity escalation

### FE9-B Auth and Welcome Identity Redesign

Goal:

- turn auth and landing into a memorable command-entry experience

Scope:

- redesign login and welcome composition
- strengthen atmosphere, asymmetry, and identity
- create a stronger relationship between brand, copy, and environment

Success criteria:

- login no longer reads as a generic white card on black
- welcome screen communicates operational identity immediately
- both surfaces feel connected to the main shell language

Priority:

- highest after FE9-A

### FE9-C Cross-Screen Shell Assimilation

Goal:

- make every authenticated screen feel like one product, not isolated islands

Scope:

- normalize page-entry rhythm after shell repair
- align top framing, section spacing, and secondary rails
- verify dashboard, incidents, settings, templates, and staff surfaces share the same structural language

Success criteria:

- shell remains consistent across all major routes
- dark shell plus light content reads as one system, not mixed themes

Priority:

- required for full identity completion

### FE9-D Premium UI Finish and Visual QA

Goal:

- close the perception gap between “good screens” and “premium product”

Scope:

- final pass on shell polish, auth entry atmosphere, and cross-screen consistency
- screenshot-based visual QA on desktop and mobile
- accessibility and interaction confirmation for key shell/navigation states

Success criteria:

- first impression matches the internal screen quality
- no screen family feels left behind

Priority:

- final completion gate

## Execution Order

1. FE9-A App Shell Architecture Repair
2. FE9-B Auth and Welcome Identity Redesign
3. FE9-C Cross-Screen Shell Assimilation
4. FE9-D Premium UI Finish and Visual QA

## Engineering Rules

1. Do not patch symptoms when structure is wrong.
2. Do not redesign local screens until the shell frame is correct.
3. Keep layout ownership obvious.
4. Reuse the existing token/component system; extend only when needed.
5. Validate by screenshots and browser smoke, not by assumption.

## Expected Outcome

After FE9 completes, the product should feel like:

- one coherent command system
- one premium visual doctrine
- one professional left-rail shell
- one branded entry experience from auth through admin and daily operations
