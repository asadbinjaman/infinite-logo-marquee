# Infinite Client Logo Marquee

A lightweight, dependency-free WordPress Gutenberg block for creating smooth, infinite-scrolling client logo tickers with multi-directional support (`Left`, `Right`, `Up`, `Down`), bulk media uploads, and custom CSS unit controls.

---

## Features

- **4 Scroll Directions:** Smooth, seamless infinite looping in **Left (`←`)**, **Right (`→`)**, **Up (`↑`)**, and **Down (`↓`)** directions.
- **Zero External Libraries:** Powered purely by hardware-accelerated CSS3 `@keyframes` (`translate3d`) and lightweight vanilla JavaScript for automatic viewport cloning.
- **Bulk Logo Management:** Upload or select multiple logos at once via the native WordPress Media Library gallery modal, with a compact thumbnail manager in the block sidebar.
- **Flexible CSS Units:** Configure logo height, spacing gaps, and vertical viewport height using `px`, `vh`, `vw`, `rem`, `em`, or `%`.
- **Customizable Motion:** Adjust scroll duration (speed) in seconds and toggle **Pause on Hover**.
- **Edge Fade Masking:** Built-in subtle gradient edge fade (`mask-image`) for clean visual entry and exit on both horizontal and vertical layouts.
- **Lightweight Editor Preview:** Displays a clean, static preview strip inside the Gutenberg editor to keep editing fast and distraction-free.

---

## Installation

### Method 1: Upload via WordPress Admin
1. Download or zip the plugin folder (`infinite-logo-marquee`).
2. In your WordPress dashboard, go to **Plugins → Add New → Upload Plugin**.
3. Upload the `.zip` file and click **Install Now**.
4. Click **Activate Plugin**.

### Method 2: Manual / Git Installation
1. Clone or copy this repository into your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone <your-repo-url> infinite-logo-marquee
   ```
2. Navigate to **Plugins** in the WordPress admin dashboard and activate **Infinite Client Logo Marquee**.

---

## Usage

1. Open any page or post in the **WordPress Block Editor (Gutenberg)**.
2. Click the **`+` (Add Block)** button and search for **Client Logo Marquee** (under the *Media* category).
3. In the right-hand **Block Inspector Sidebar**:
   - Click **Upload / Select Logos** to bulk-add logos from your Media Library.
   - Adjust **Logo Absolute Height**, **Gap Between Logos**, and **Scroll Duration (Seconds)**.
   - Choose your **Scroll Direction** (`Left`, `Right`, `Up`, or `Down`).
   - If `Up` or `Down` is selected, set the **Marquee Viewport Height** to define the visible container height.
   - Toggle **Pause on Hover** as needed.
4. Publish or update your page.

---

## Block Attributes

| Attribute | Type | Default | Description |
| :--- | :--- | :--- | :--- |
| `images` | `array` | `[]` | Array of selected logo objects (`id`, `url`, `alt`). |
| `logoHeight` | `string` | `'50px'` | Fixed height for each logo (`width` scales automatically to preserve aspect ratio). |
| `logoGap` | `string` | `'48px'` | Spacing between adjacent logos. |
| `speed` | `number` | `30` | Animation duration in seconds (lower = faster, higher = slower). |
| `pauseOnHover` | `boolean` | `true` | Pauses the marquee animation when hovered. |
| `direction` | `string` | `'left'` | Scroll direction: `'left'`, `'right'`, `'up'`, or `'down'`. |
| `wrapperHeight` | `string` | `'200px'` | Visible container height (applied when direction is `'up'` or `'down'`). |

---

## File Structure

```text
infinite-logo-marquee/
├── infinite-logo-marquee.php   # Block registration, server-side rendering, and dynamic cloning script
├── block.js                    # Gutenberg editor UI, InspectorControls, and MediaUpload integration
├── style.css                   # Frontend layout, edge masking, and 4-way CSS keyframe animations
├── editor.css                  # Gutenberg editor canvas and sidebar thumbnail grid styles
└── README.md                   # Repository documentation
```

---

## Customization

### Removing the Edge Fade Effect
By default, `.iclm-marquee-wrapper` applies a subtle 5% edge gradient fade via `mask-image`. To use hard edges instead, override it in your theme's CSS:

```css
.iclm-marquee-wrapper {
    -webkit-mask-image: none !important;
    mask-image: none !important;
}
```

### Grayscale / Hover Logo Styling
You can style the logos using the `.iclm-logo-item img` selector:

```css
.iclm-logo-item img {
    filter: grayscale(100%);
    opacity: 0.7;
    transition: filter 0.3s ease, opacity 0.3s ease;
}

.iclm-logo-item img:hover {
    filter: grayscale(0%);
    opacity: 1;
}
```

---

## Requirements

- **WordPress:** 5.8 or higher (Gutenberg block editor)
- **PHP:** 7.4 or higher

---

## License

This project is open-source and available under the [GPL-2.0-or-later](https://www.gnu.org/licenses/gpl-2.0.html) license.
