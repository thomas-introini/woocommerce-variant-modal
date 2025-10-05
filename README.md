# WooCommerce Variant Modal

Open a modal on non–single product pages (shop, category, related, etc.) for **variable products** so customers can choose attributes, see the live price, and add to cart via AJAX — without leaving the page. Fully customizable from the admin, responsive, and accessible.

> **Requires:** WordPress 6.2+, WooCommerce 7.0+, PHP 7.4+
> **Tested with:** WooCommerce 9.1

---

## Features

- 🛒 Intercepts **“Select options / Choose”** for variable products outside the single product page
- 🔁 Uses Woo’s **native variation form & JS** (live price/stock updates, compatibility-friendly)
- ⚡ **AJAX add to cart** with fragment refresh (mini-cart, counters, notices)
- 📱 **Responsive & accessible** (ARIA roles, focus trap, ESC/backdrop close)
- 🎨 **Customizable styles** (colors, overlay, radius, custom CSS)
- 🔧 Useful toggles: show **quantity**, **stock**, **SKU**, lock scroll, etc.
- 🧩 Plays nicely with most “variation swatches” plugins (since it uses core markup/hooks)

---

## Screenshots

> _Add your own screenshots or GIFs here (e.g., `/docs/` or GitHub assets)._

---

## Installation

1. Download or clone this repository into: `wp-content/plugins/woocommerce-variant-modal`
2. Activate **WooCommerce Variant Modal** from **Plugins → Installed Plugins**.
3. Configure under **WooCommerce → Variant Modal**.

> You can also zip the folder and upload via **Plugins → Add New → Upload Plugin**.

---

## Usage

Once activated, archive buttons for **variable** products will open the modal instead of navigating to the single product page. The modal shows attribute dropdowns, dynamic price/stock, quantity (optional), and an **Add to cart** button that uses WooCommerce’s AJAX endpoint.

On **single product pages** the behavior is unchanged (normal WooCommerce single product flow).

---

## Settings

**WooCommerce → Variant Modal**

- **General**
- **Enable**
- **Enable on archives** (shop, categories, tags, related/upsells/shortcodes)
- **Archive button text** (default: “Choose options”)
- **Behavior**
- **Show quantity**
- **Show stock status**
- **Show SKU**
- **Close on ESC**
- **Close on backdrop click**
- **Lock page scroll while open**
- **Style**
- **Primary color**
- **Accent color**
- **Text color**
- **Modal background**
- **Overlay color**
- **Overlay opacity** (0–1)
- **Corner radius** (px)
- **Custom CSS** (free-form CSS appended inline)

---

## Theming (CSS Variables)

The frontend exposes CSS variables for quick theming:

```css
:root{
--wcvm-primary: #1a73e8;
--wcvm-accent: #0b57d0;
--wcvm-text: #111827;
--wcvm-bg: #ffffff;
--wcvm-overlay: #000000;
--wcvm-overlay-opacity: 0.55;
--wcvm-radius: 14px;
}

