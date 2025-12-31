I will optimize the `CommentSection.vue` component to fully leverage the existing high-quality design system in `app.css`. This ensures WCAG AA compliance, perfect dark mode support, and consistent styling.

### 1. Refactor `CommentSection.vue`
- **Replace Hardcoded Colors:** Replace raw Tailwind colors (e.g., `text-gray-500`, `bg-gray-50`) with the project's semantic classes (`text-primary`, `text-muted`, `bg-card`, `bg-elevated`).
- **Improve Contrast:**
    - Comment text: `text-gray-800` -> `text-secondary` (Slate 700/300).
    - Metadata (Date/Time): `text-gray-500` -> `text-muted` (Slate 500/400).
    - Backgrounds: `bg-gray-50` -> `bg-elevated` (ensures correct shade in both modes).
- **Form Styling:** Update the textarea and input fields to use `input-theme` class which handles borders, backgrounds, and focus states consistently.

### 2. Styling Details
- **Comment Bubbles:** Use `bg-elevated` combined with `border-theme` to create distinct but accessible comment containers.
- **Dark Mode:** The use of semantic classes (`text-primary`, `bg-card`) will automatically handle `prefers-color-scheme` via the variables defined in `app.css`.

### 3. Verification
- I will verify that the new classes map to the high-contrast variables defined in `app.css` (e.g., ensuring text is at least 4.5:1 contrast ratio).
