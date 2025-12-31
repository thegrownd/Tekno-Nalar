# Comprehensive Testing Guide
## Website Redesign & Image Upload Fix

---

## 🧪 Testing Checklist

### Prerequisites
1. ✅ Ensure Laravel server is running: `php artisan serve`
2. ✅ Ensure Vite is running: `npm run dev`
3. ✅ Clear browser cache
4. ✅ Open browser developer tools (F12)

---

## 1. IMAGE UPLOAD COMPONENT TESTING

### Test 1.1: File Selection (Click to Browse)
**Steps:**
1. Navigate to article creation page (`/articles/new`)
2. Click on the upload area
3. Select a valid image file (JPEG, PNG, or GIF)

**Expected Results:**
- ✅ File picker dialog opens
- ✅ Preview appears immediately after selection
- ✅ File name and size are displayed
- ✅ "Preview Siap" badge appears on image
- ✅ Upload button is enabled

### Test 1.2: Drag & Drop Functionality
**Steps:**
1. Navigate to article creation page
2. Drag an image file from your computer
3. Drop it on the upload area

**Expected Results:**
- ✅ Upload area highlights when dragging over it
- ✅ Upload area scales up slightly (scale-105)
- ✅ Preview appears immediately after drop
- ✅ Same behavior as click-to-browse

### Test 1.3: Preview Display
**Steps:**
1. Upload an image using either method
2. Observe the preview section

**Expected Results:**
- ✅ Image preview displays immediately
- ✅ Preview is contained within max-height of 96 (24rem)
- ✅ Image maintains aspect ratio
- ✅ Green "Preview Siap" badge visible
- ✅ File info shows name and size

### Test 1.4: File Validation - Invalid Format
**Steps:**
1. Try to upload a non-image file (e.g., .txt, .pdf, .doc)

**Expected Results:**
- ✅ Error message appears: "Format tidak didukung. Gunakan JPG, PNG, atau GIF."
- ✅ Error message has red background
- ✅ No preview is shown
- ✅ Upload button remains disabled

### Test 1.5: File Validation - Oversized File
**Steps:**
1. Try to upload an image larger than 5MB

**Expected Results:**
- ✅ Error message appears: "Ukuran file melebihi 5MB. Silakan pilih gambar yang lebih kecil."
- ✅ Error message has red background
- ✅ No preview is shown

### Test 1.6: Image Editing Features
**Steps:**
1. Upload a valid image
2. Expand the "Edit Gambar (Opsional)" section
3. Test rotation buttons (90°, 180°, 270°)
4. Adjust quality slider (50-100%)
5. Adjust zoom slider (50-150%)

**Expected Results:**
- ✅ Details section expands/collapses smoothly
- ✅ Canvas shows edited version
- ✅ Rotation applies correctly
- ✅ Quality slider updates percentage display
- ✅ Zoom slider updates percentage display
- ✅ Changes are reflected in canvas

### Test 1.7: Upload Progress
**Steps:**
1. Upload a valid image
2. Click "Upload ke Server" button
3. Observe progress indicator

**Expected Results:**
- ✅ Button shows spinning icon
- ✅ Button text changes to "Mengunggah..."
- ✅ Progress bar appears with percentage
- ✅ Progress bar fills from 0% to 100%
- ✅ Button is disabled during upload

### Test 1.8: Upload Success
**Steps:**
1. Complete an image upload successfully

**Expected Results:**
- ✅ Success message appears: "Gambar berhasil diunggah dan siap digunakan!"
- ✅ Success message has green background
- ✅ Button shows checkmark icon
- ✅ Button text changes to "Upload Berhasil!"
- ✅ Preview remains visible with uploaded URL
- ✅ Button becomes disabled (upload complete)

### Test 1.9: Upload Error Handling
**Steps:**
1. Disconnect from internet or stop Laravel server
2. Try to upload an image

**Expected Results:**
- ✅ Error message appears with specific error
- ✅ Error message has red background
- ✅ Progress resets to 0%
- ✅ Button returns to normal state
- ✅ User can retry upload

### Test 1.10: Remove/Clear Functionality
**Steps:**
1. Upload an image (with or without uploading to server)
2. Click the "Hapus" button

**Expected Results:**
- ✅ Preview disappears
- ✅ Upload area reappears
- ✅ All states reset (progress, success, error)
- ✅ File input is cleared
- ✅ Can upload a new image

### Test 1.11: Change Image
**Steps:**
1. Upload an image
2. Expand edit section
3. Click "Ganti Gambar" button

**Expected Results:**
- ✅ File picker opens
- ✅ Can select a new image
- ✅ New image replaces old preview
- ✅ Upload state resets

---

## 2. CSS/DESIGN CHANGES TESTING

### Test 2.1: Animations - Fade In
**Steps:**
1. Navigate to homepage
2. Observe article cards loading

**Expected Results:**
- ✅ Cards fade in smoothly
- ✅ Cards have slight upward movement during fade
- ✅ Animation duration is ~500ms

### Test 2.2: Animations - Slide In
**Steps:**
1. Navigate to homepage
2. Observe header and sidebar elements

**Expected Results:**
- ✅ Header slides in from left
- ✅ Sidebar slides in from right
- ✅ Smooth 600ms animation

### Test 2.3: Animations - Staggered
**Steps:**
1. Navigate to articles page
2. Observe multiple article cards

**Expected Results:**
- ✅ Cards appear with staggered delays
- ✅ Each card has 0.1s delay increment
- ✅ Creates cascading effect

### Test 2.4: Hover Effects - Card Lift
**Steps:**
1. Hover over article cards

**Expected Results:**
- ✅ Card lifts up (translateY -8px)
- ✅ Card scales slightly (1.02)
- ✅ Shadow becomes more prominent
- ✅ Smooth transition

### Test 2.5: Hover Effects - Button Glow
**Steps:**
1. Hover over gradient buttons

**Expected Results:**
- ✅ Button lifts up slightly
- ✅ Glow effect appears around button
- ✅ Smooth transition
- ✅ Ripple effect on before pseudo-element

### Test 2.6: Hover Effects - CTA Button
**Steps:**
1. Hover over "Tambah Artikel" button

**Expected Results:**
- ✅ Button lifts up (translateY -2px)
- ✅ Background color darkens slightly
- ✅ Ripple effect expands from center
- ✅ Shadow increases

### Test 2.7: Input Focus States
**Steps:**
1. Click on any input field (search, form inputs)

**Expected Results:**
- ✅ Border color changes to cyan
- ✅ Subtle glow appears (box-shadow)
- ✅ Outline is removed (custom focus style)
- ✅ Smooth transition

### Test 2.8: Theme Switching - Light to Dark
**Steps:**
1. Click theme toggle button
2. Observe all elements

**Expected Results:**
- ✅ Background changes to dark slate
- ✅ Text colors invert appropriately
- ✅ Cards have dark background
- ✅ Borders adjust to dark theme
- ✅ Shadows adjust for dark theme
- ✅ Smooth 300ms transition
- ✅ All text remains readable (WCAG AAA)

### Test 2.9: Theme Switching - Dark to Light
**Steps:**
1. Switch back to light theme

**Expected Results:**
- ✅ Background returns to white
- ✅ Text colors return to dark
- ✅ All elements transition smoothly
- ✅ No jarring color changes

### Test 2.10: Responsive Design - Mobile (< 640px)
**Steps:**
1. Resize browser to mobile width
2. Navigate through pages

**Expected Results:**
- ✅ Navbar collapses to hamburger menu
- ✅ Search bar moves to mobile menu
- ✅ Article cards stack vertically
- ✅ Footer columns stack
- ✅ All text remains readable
- ✅ Touch targets are adequate (44x44px minimum)

### Test 2.11: Responsive Design - Tablet (640px - 1024px)
**Steps:**
1. Resize browser to tablet width

**Expected Results:**
- ✅ 2-column grid for articles
- ✅ Navbar shows some items
- ✅ Proper spacing maintained
- ✅ Images scale appropriately

### Test 2.12: Responsive Design - Desktop (> 1024px)
**Steps:**
1. View on full desktop width

**Expected Results:**
- ✅ 3-column grid for articles
- ✅ Full navbar visible
- ✅ Optimal reading width maintained
- ✅ Proper use of whitespace

### Test 2.13: Color Contrast - Light Theme
**Steps:**
1. Use browser accessibility tools or contrast checker
2. Check text against backgrounds

**Expected Results:**
- ✅ Primary text: 15.52:1 contrast (WCAG AAA)
- ✅ Secondary text: 9.73:1 contrast (WCAG AAA)
- ✅ Muted text: 5.05:1 contrast (WCAG AA)
- ✅ Links: 7.04:1 contrast (WCAG AAA)

### Test 2.14: Color Contrast - Dark Theme
**Steps:**
1. Switch to dark theme
2. Check text against backgrounds

**Expected Results:**
- ✅ Primary text: 15.52:1 contrast (WCAG AAA)
- ✅ Secondary text: 9.73:1 contrast (WCAG AAA)
- ✅ Muted text: 5.05:1 contrast (WCAG AA)
- ✅ Links: 7.04:1 contrast (WCAG AAA)

### Test 2.15: Keyboard Navigation
**Steps:**
1. Use Tab key to navigate through page
2. Use Enter/Space to activate buttons

**Expected Results:**
- ✅ Focus indicator visible on all interactive elements
- ✅ Focus indicator has 2px cyan outline
- ✅ Tab order is logical
- ✅ All buttons/links accessible via keyboard
- ✅ No keyboard traps

### Test 2.16: Skeleton Loading
**Steps:**
1. Navigate to articles page
2. Observe loading state (may need to throttle network)

**Expected Results:**
- ✅ Skeleton placeholders appear
- ✅ Shimmer animation runs smoothly
- ✅ Skeleton matches content layout
- ✅ Smooth transition to actual content

### Test 2.17: Scrollbar Styling
**Steps:**
1. Scroll through long pages

**Expected Results:**
- ✅ Custom scrollbar appears (10px width)
- ✅ Scrollbar track matches theme
- ✅ Scrollbar thumb is visible
- ✅ Hover changes thumb to cyan
- ✅ Smooth scrolling enabled

---

## 3. INTEGRATION TESTING

### Test 3.1: Article Creation Flow
**Steps:**
1. Navigate to `/articles/new`
2. Fill in title
3. Upload an image
4. Select category
5. Fill in content
6. Submit form

**Expected Results:**
- ✅ All fields validate correctly
- ✅ Image preview shows throughout
- ✅ Form submits successfully
- ✅ Redirects to article list
- ✅ New article appears with uploaded image

### Test 3.2: Article Edit Flow
**Steps:**
1. Navigate to existing article
2. Click edit button
3. Change image
4. Update other fields
5. Submit

**Expected Results:**
- ✅ Existing image shows in preview
- ✅ Can replace image
- ✅ New image uploads successfully
- ✅ Article updates correctly

### Test 3.3: Image Display in Articles
**Steps:**
1. View article list
2. View article detail
3. Check image display

**Expected Results:**
- ✅ Images load correctly
- ✅ Images have proper aspect ratio
- ✅ Images are responsive
- ✅ Lazy loading works (if implemented)

### Test 3.4: URL Method vs Upload Method
**Steps:**
1. Create article with URL method
2. Create article with upload method
3. Compare results

**Expected Results:**
- ✅ Both methods work correctly
- ✅ Images display identically
- ✅ No difference in functionality

---

## 4. CROSS-BROWSER TESTING

### Test 4.1: Chrome/Edge
**Steps:**
1. Test all above scenarios in Chrome/Edge

**Expected Results:**
- ✅ All features work correctly
- ✅ Animations smooth
- ✅ No console errors

### Test 4.2: Firefox
**Steps:**
1. Test all above scenarios in Firefox

**Expected Results:**
- ✅ All features work correctly
- ✅ Backdrop blur works
- ✅ Animations smooth

### Test 4.3: Safari
**Steps:**
1. Test all above scenarios in Safari

**Expected Results:**
- ✅ All features work correctly
- ✅ -webkit- prefixes work
- ✅ Animations smooth

### Test 4.4: Mobile Browsers
**Steps:**
1. Test on actual mobile devices or emulators

**Expected Results:**
- ✅ Touch interactions work
- ✅ Responsive design correct
- ✅ Performance acceptable

---

## 5. PERFORMANCE TESTING

### Test 5.1: Page Load Time
**Steps:**
1. Open DevTools Network tab
2. Hard refresh page (Ctrl+Shift+R)
3. Check load times

**Expected Results:**
- ✅ Initial load < 3 seconds
- ✅ CSS loads quickly
- ✅ No render-blocking resources

### Test 5.2: Animation Performance
**Steps:**
1. Open DevTools Performance tab
2. Record while scrolling and interacting
3. Check for jank

**Expected Results:**
- ✅ 60fps maintained
- ✅ No layout thrashing
- ✅ Smooth animations

### Test 5.3: Image Upload Performance
**Steps:**
1. Upload various image sizes
2. Monitor network and performance

**Expected Results:**
- ✅ Small images (< 1MB) upload quickly
- ✅ Large images (< 5MB) show progress
- ✅ No memory leaks

---

## 6. ERROR SCENARIOS

### Test 6.1: Network Failure During Upload
**Steps:**
1. Start image upload
2. Disconnect network mid-upload

**Expected Results:**
- ✅ Error message appears
- ✅ Can retry upload
- ✅ No broken state

### Test 6.2: Server Error
**Steps:**
1. Stop Laravel server
2. Try to upload image

**Expected Results:**
- ✅ Appropriate error message
- ✅ User can retry
- ✅ No console errors

### Test 6.3: Invalid Session
**Steps:**
1. Clear session/token
2. Try to upload image

**Expected Results:**
- ✅ Authentication error shown
- ✅ Redirects to login (if applicable)

---

## 📊 TEST RESULTS TEMPLATE

Use this template to record your test results:

```
## Test Session: [Date/Time]
Browser: [Chrome/Firefox/Safari/etc.]
Screen Size: [Desktop/Tablet/Mobile]

### Image Upload Tests
- [ ] Test 1.1: File Selection - PASS/FAIL
- [ ] Test 1.2: Drag & Drop - PASS/FAIL
- [ ] Test 1.3: Preview Display - PASS/FAIL
- [ ] Test 1.4: Invalid Format - PASS/FAIL
- [ ] Test 1.5: Oversized File - PASS/FAIL
- [ ] Test 1.6: Image Editing - PASS/FAIL
- [ ] Test 1.7: Upload Progress - PASS/FAIL
- [ ] Test 1.8: Upload Success - PASS/FAIL
- [ ] Test 1.9: Upload Error - PASS/FAIL
- [ ] Test 1.10: Remove/Clear - PASS/FAIL
- [ ] Test 1.11: Change Image - PASS/FAIL

### CSS/Design Tests
- [ ] Test 2.1: Fade In Animation - PASS/FAIL
- [ ] Test 2.2: Slide In Animation - PASS/FAIL
- [ ] Test 2.3: Staggered Animation - PASS/FAIL
- [ ] Test 2.4: Card Lift Hover - PASS/FAIL
- [ ] Test 2.5: Button Glow Hover - PASS/FAIL
- [ ] Test 2.6: CTA Button Hover - PASS/FAIL
- [ ] Test 2.7: Input Focus - PASS/FAIL
- [ ] Test 2.8: Light to Dark Theme - PASS/FAIL
- [ ] Test 2.9: Dark to Light Theme - PASS/FAIL
- [ ] Test 2.10: Mobile Responsive - PASS/FAIL
- [ ] Test 2.11: Tablet Responsive - PASS/FAIL
- [ ] Test 2.12: Desktop Responsive - PASS/FAIL
- [ ] Test 2.13: Light Theme Contrast - PASS/FAIL
- [ ] Test 2.14: Dark Theme Contrast - PASS/FAIL
- [ ] Test 2.15: Keyboard Navigation - PASS/FAIL
- [ ] Test 2.16: Skeleton Loading - PASS/FAIL
- [ ] Test 2.17: Scrollbar Styling - PASS/FAIL

### Integration Tests
- [ ] Test 3.1: Article Creation - PASS/FAIL
- [ ] Test 3.2: Article Edit - PASS/FAIL
- [ ] Test 3.3: Image Display - PASS/FAIL
- [ ] Test 3.4: URL vs Upload - PASS/FAIL

### Notes:
[Add any issues found or observations]
```

---

## 🐛 ISSUE REPORTING

If you find any issues during testing, please document:

1. **Issue Description:** Clear description of the problem
2. **Steps to Reproduce:** Exact steps to recreate the issue
3. **Expected Behavior:** What should happen
4. **Actual Behavior:** What actually happens
5. **Browser/Device:** Where the issue occurs
6. **Screenshots:** If applicable
7. **Console Errors:** Any JavaScript errors

---

## ✅ SIGN-OFF

After completing all tests, sign off:

```
Tested By: [Your Name]
Date: [Date]
Overall Status: PASS/FAIL
Ready for Production: YES/NO

Summary:
[Brief summary of test results and any critical issues]
