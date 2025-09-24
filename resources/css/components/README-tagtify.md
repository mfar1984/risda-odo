# Tagtify Component

Centralized CSS component untuk styling Tagtify library yang mengikut design pattern form-select dan dropdown components dalam sistem RISDA.

## Penggunaan

1. Import Tagify CSS library:
```html
<link href="https://cdn.jsdelivr.net/npm/@yaireo/tagify/dist/tagify.css" rel="stylesheet" type="text/css" />
<script src="https://cdn.jsdelivr.net/npm/@yaireo/tagify"></script>
```

2. Styling akan automatically apply kepada semua `.tagify` elements kerana sudah included dalam `app.css`.

## Variants

### Standard
Default styling yang match dengan form-select:
```html
<input id="tags" class="form-select mt-1 block w-full" />
```

### Small
Untuk forms yang lebih kecil:
```html
<input id="tags" class="tagify-sm form-select mt-1 block w-full" />
```

### Large  
Untuk forms yang lebih besar:
```html
<input id="tags" class="tagify-lg form-select mt-1 block w-full" />
```

### Error State
Untuk validation errors:
```html
<input id="tags" class="tagify-error form-select mt-1 block w-full" />
```

### Success State
Untuk successful validation:
```html
<input id="tags" class="tagify-success form-select mt-1 block w-full" />
```

## Features

- ✅ Matches form-select design exactly
- ✅ Consistent typography (Poppins font)
- ✅ Professional blue tags (#1e40af)
- ✅ Hover and focus states
- ✅ Responsive design
- ✅ Disabled state support
- ✅ Loading state with spinner
- ✅ No animations for clean UX

## JavaScript Initialization

```javascript
const tagify = new Tagify(inputElement, {
    whitelist: data,
    maxTags: 10,
    dropdown: {
        maxItems: 20,
        enabled: 0,
        closeOnSelect: false
    }
});
```

## Colors Used

- Primary: #1e40af (blue-800)
- Hover: #1d4ed8 (blue-700) 
- Border: #d1d5db (gray-300)
- Focus: #3b82f6 (blue-500)
- Text: #374151 (gray-700)
- Placeholder: #9ca3af (gray-400)
