# Changelog

All notable changes to this project will be documented in this file.

## [Unreleased] - 2025-12-28

### Changed
- **UI/UX (Comment Section)**: 
  - Updated comment input placeholder color to white (`#FFFFFF`) in Dark Mode to meet WCAG AA contrast standards (4.5:1).
  - Added smooth transition (`duration-300`) for theme switching.
- **UI/UX (Article Detail)**:
  - Redesigned Author Profile Logo (Avatar) for better visibility in Light Mode.
  - Added `shadow-xl` and `ring-2` (white ring in light mode, cyan ring in dark mode) to the avatar.
  - Implemented responsive styling respecting `prefers-color-scheme` via Tailwind's dark mode utilities.
  - **Author Logo Redesign**:
    - Implemented a modern, glowing gradient background (`cyan-500` to `blue-600`) that remains consistent in both Light and Dark modes.
    - Added a stylish blur effect (`blur` + `opacity`) for a glowing aura.
    - Increased size to `w-14 h-14` (56px) for better proportionality.
    - Added high-contrast ring (`ring-white` in light, `ring-gray-800` in dark) to separate the logo from the page background.
