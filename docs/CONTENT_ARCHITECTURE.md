# Content Architecture

## 1. Purpose

This document defines the content and information architecture of the **WREN WOLD** fashion e-commerce website.

It describes:

- Main pages
- Navigation
- Commerce structure
- Homepage sections
- Product content
- Editorial content
- Digital resources
- SEO-oriented content structure
- MVP versus future functionality

The goal is to create a clear and scalable content ecosystem without unnecessary complexity.

---

## 2. Core Content Principle

The website is primarily an e-commerce platform.

Content exists to:

1. Help customers discover products.
2. Build trust.
3. Explain the brand.
4. Help customers make better fashion decisions.
5. Support SEO.
6. Create long-term customer relationships.

The website should not become a traditional content-heavy blog.

Editorial content should support the commerce experience.

---

## 3. Primary Website Structure

Approved information architecture:

```text
Home
│
├── Shop
│   ├── All Products
│   ├── T-Shirts
│   ├── Hoodies
│   ├── Knitwear
│   ├── Shirts
│   ├── Blouses
│   ├── Pants
│   ├── Skirts
│   ├── Dresses
│   └── Coats
│
├── Collections
│   ├── Everyday Essentials
│   └── Occasion / Evening Wear
│
├── Guides
│
├── About
│
└── Contact

Utility Navigation:
├── Search
├── Cart
└── My Account
```

> **Resolved 2026-08-27:** An earlier draft of this document listed "Tops",
> "Everyday Essentials" and "Occasion / Evening Wear" as flat Shop categories
> alongside garment-type categories. This created overlapping taxonomy (an
> occasion-based grouping and a garment-type grouping at the same level).
> Decision: Shop categories are garment-type only (T-Shirts, Hoodies,
> Knitwear, Shirts, Pants, Dresses — "Tops" dropped as redundant with
> Shirts/Knitwear/T-Shirts). "Everyday Essentials" and "Occasion / Evening
> Wear" moved under **Collections**, implemented as WooCommerce product tags
> (cross-category, non-exclusive groupings) rather than product categories.

> **Resolved 2026-09-21:** Added three new Shop categories — Blouses, Skirts,
> and Coats — after full analysis of the Matterhorn supplier feed showed
> these are genuinely distinct garment types with meaningful product volume,
> not redundant with existing categories. "Tops" remains intentionally
> excluded (per the 2026-08-27 resolution above) — general t-shirts and
> bodysuits fold into the existing T-Shirts category instead. Full
> category-to-supplier-feed mapping is documented in the wren-wold-ai-agent
> plugin repo, docs/04-decisions.md.

---

## 4. Primary Navigation

Approved primary navigation (MVP):

- Shop
- Collections
- Guides
- About
- Contact

Shop categories are nested under Shop. Categories must NOT appear as a separate top-level navigation item.

Collections remains a separate top-level section.

Guides will be called "Guides" for now.

---

## 5. Utility Navigation

The following belong to utility navigation, not the primary menu:

- Search
- Cart
- My Account

---

## 6. Shop Categories

Approved shop categories (garment type — WooCommerce product categories):

- T-Shirts
- Hoodies
- Knitwear
- Shirts
- Blouses
- Pants
- Skirts
- Dresses
- Coats

These categories live under Shop and are used for product discovery and archive pages. This taxonomy is garment-type only; occasion-based groupings do not belong here (see Section 6a).

---

## 6a. Collections

Collections are cross-category, occasion-based groupings, implemented as WooCommerce product tags (not categories), since a single product may belong to a garment-type category and a collection at the same time (e.g. a T-Shirt can also be an Everyday Essential).

Approved collections (MVP):

- Everyday Essentials
- Occasion / Evening Wear

Collections appear as a top-level primary navigation item with these two as dropdown children, resolving to the corresponding `product_tag` archive.

---

## 7. Reviews (MVP)

Reviews will NOT be a top-level navigation item for the MVP.

Customer reviews should primarily appear on product pages.

---

## Wishlist (as of v0.4.38)

Wishlist is account-based only (WooCommerce customer meta `_fbt_wishlist`), no guest/localStorage fallback. Guests clicking the wishlist heart see a "Sign in to save favorites" prompt and nothing is saved. A future iteration may add a localStorage fallback for guests that merges into their account on login — not implemented yet, deferred by owner decision.
