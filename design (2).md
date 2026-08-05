# Design System Guidelines (Google Stitch / Stitch Prompting)

## Overview
This document defines the UI/UX design system extracted from the provided dashboard layout reference, re-mapped dynamically into the new Forest Sage color palette. It is structured to be directly fed into **Google Stitch** or web design generation workflows.

---

## 1. Color Palette Mapping (Forest Sage Scheme)

We translate the original purple UI components (dark sidebar, active states, cards, accents) directly into the new green palette:

| Component / Token | Original Role (Purple Dashboard) | New Color Code | Visual Role & Usage |
| :--- | :--- | :--- | :--- |
| **Primary Dark / Sidebar** | Deep Purple (`#0F0A28`) | `#0F2A1D` | Main Sidebar Background, Dark Cards, Dark Badges |
| **Primary Accent / Brand** | Vivid Purple (`#7C5CFC`) | `#375534` | Secondary Dark Panels, Primary Buttons, Active Hover States |
| **Secondary Accent / Soft** | Soft Purple/Violet (`#A28BFA`) | `#6B9071` | Active Navigation Fill, Selected Pill Indicators, Progress Fills |
| **Light Muted / Borders** | Muted Grey-Violet (`#E2E0F5`) | `#AEC3B0` | Input Borders, Divider Lines, Card Outline Stroking |
| **Light Surface / Background** | Clean Off-White (`#F6F8FB`) | `#E3EED4` | Main Content Area Background, Light Card Fills |
| **Card White** | Pure White (`#FFFFFF`) | `#FFFFFF` | Main Metric Cards, Chart Containers, Dropdown Panels |
| **Text Primary** | Dark Slate (`#1A1D26`) | `#0F2A1D` | Headings, Metric Values, Active Label Text |
| **Text Secondary / Muted** | Grey (`#7E84A3`) | `#5C6E60` | Subtitles, Table Headers, Inactive Navigation Labels |
| **Success Indicator** | Mint Green (`#10B981`) | `#2E7D32` | Positive trend tags (+10%, Growth metrics) |
| **Warning / Alert** | Coral Red (`#FF6B6B`) | `#D32F2F` | Negative trend tags, Alert counts, Discontinued metrics |

---

## 2. Component Mapping & Layout Architecture

### A. Navigation Sidebar (Left Column)
- **Background:** `#0F2A1D` (Dark Forest Green)
- **Width:** 260px fixed width, full height (`100vh`)
- **Logo Area:** White text / icon (`#FFFFFF`), bold font size 20px
- **Navigation Links:**
  - **Inactive Items:** Text color `#AEC3B0` with transparent background. Hover state background `#375534` (10% opacity).
  - **Active Item ("Dashboard"):** Background color `#6B9071` (Sage Green) with smooth rounded pill border (`border-radius: 12px`), text/icon in white (`#FFFFFF`).
- **Footer Section:** Lower menu items ("Help", "Log Out") aligned at bottom with subtle border line above (`border-top: 1px solid rgba(255,255,255,0.1)`).

### B. Header / Top Navigation Bar
- **Background:** Transparent or `#E3EED4` (Matching main content background)
- **Search Bar:**
  - Background `#FFFFFF`
  - Border radius `20px` (Pill shape)
  - Border `#AEC3B0`
  - Text placeholder `#5C6E60`
- **Actions & Profile:** Right-aligned notification icon in rounded white badge, user avatar with name and dropdown arrow.

### C. Dashboard Content Cards & Grid Structure
- **Grid Layout:** 12-column responsive layout, 24px gap.
- **Card Styling:**
  - Background: `#FFFFFF`
  - Corner Radius: `16px`
  - Shadow: `0px 4px 20px rgba(15, 42, 29, 0.05)`
  - Padding: `24px`

#### Metric Cards (KPI Stats)
- **4-Column Row:** Total Shipment, On Going Air Freight, Ocean Freight, Road Freight.
- **Icon Container:** Rounded square (`40px x 40px`, radius `12px`) with soft tint (`#E3EED4`) and dark green icon (`#0F2A1D`).
- **Typography:**
  - Metric Number: `28px`, Bold (`#0F2A1D`)
  - Subtitle / Trend: `12px`, `#2E7D32` for positive %, `#D32F2F` for negative %.

#### Feature Highlight Card (Bottom Right Banner - e.g., Customer Growth)
- **Background:** `#0F2A1D` (Dark Green Dark Card)
- **Accent Button/Badge:** Vibrant accent `#6B9071` with light green text `#E3EED4`.
- **Progress Bar:** High contrast container with pill radius (`50px`), filled with `#6B9071`.

---

## 3. Typography & Styling Rules

```css
/* Core Styling Rules for Stitch Implementation */

:root {
  --color-dark-bg: #0F2A1D;
  --color-primary-green: #375534;
  --color-sage: #6B9071;
  --color-light-sage: #AEC3B0;
  --color-surface-bg: #E3EED4;
  --color-card-white: #FFFFFF;
  --color-text-main: #0F2A1D;
  --color-text-muted: #5C6E60;
  --color-success: #2E7D32;
  --color-danger: #D32F2F;

  --font-family: 'Plus Jakarta Sans', 'Inter', sans-serif;
  --radius-card: 16px;
  --radius-pill: 50px;
  --radius-button: 12px;
}

body {
  background-color: var(--color-surface-bg);
  font-family: var(--font-family);
  color: var(--color-text-main);
  margin: 0;
  padding: 0;
}

.sidebar {
  background-color: var(--color-dark-bg);
  color: #FFFFFF;
  border-radius: 0 24px 24px 0;
}

.nav-item.active {
  background-color: var(--color-sage);
  color: #FFFFFF;
  border-radius: var(--radius-button);
}

.card {
  background-color: var(--color-card-white);
  border-radius: var(--radius-card);
  box-shadow: 0 4px 16px rgba(15, 42, 29, 0.04);
  padding: 24px;
}

.dark-card {
  background-color: var(--color-dark-bg);
  color: #FFFFFF;
  border-radius: var(--radius-card);
}
```

---

## 4. Stitch Prompt / Specification Summary
To instruct Google Stitch or an AI UI Generator to build this screen, use the following prompt:

> "Generate a modern SaaS analytics dashboard web app with a left fixed dark sidebar (`#0F2A1D`) featuring pill-shaped active menu highlights (`#6B9071`). The main canvas background should be soft sage green (`#E3EED4`). Include rounded white data cards (`#FFFFFF`) with subtle shadows, clean line charts, bar graphs, radial progress charts, and metric stats cards. Use dark green (`#0F2A1D`) for main headings and soft muted green (`#5C6E60`) for secondary text. Incorporate a highlighted dark feature card (`#0F2A1D`) at the bottom right corner with progress indicators in sage green (`#6B9071`). Typography should be modern, clean, and sans-serif."
