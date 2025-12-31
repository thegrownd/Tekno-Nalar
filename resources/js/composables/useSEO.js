import { watchEffect } from 'vue';

export function useSEO(title, description, image, type = 'website', schema = null) {
    watchEffect(() => {
        // Update Title
        document.title = title.value 
            ? `${title.value} - TeknoNalar` 
            : 'TeknoNalar - Edukasi & Analisis Teknologi';

        // Helper to update or create meta tags
        const updateMeta = (name, content, attribute = 'name') => {
            if (!content) return;
            let tag = document.querySelector(`meta[${attribute}="${name}"]`);
            if (!tag) {
                tag = document.createElement('meta');
                tag.setAttribute(attribute, name);
                document.head.appendChild(tag);
            }
            tag.setAttribute('content', content);
        };

        // Standard Meta
        updateMeta('description', description.value || 'TeknoNalar - Platform edukasi teknologi, cyber security, dan pemrograman.');

        // Open Graph
        updateMeta('og:title', title.value || 'TeknoNalar', 'property');
        updateMeta('og:description', description.value || 'TeknoNalar - Platform edukasi teknologi.', 'property');
        updateMeta('og:image', image.value || '/images/logo.png', 'property'); // Fallback image
        updateMeta('og:type', type, 'property');
        updateMeta('og:url', window.location.href, 'property');
        updateMeta('og:site_name', 'TeknoNalar', 'property');

        // Twitter Card
        updateMeta('twitter:card', 'summary_large_image');
        updateMeta('twitter:title', title.value || 'TeknoNalar');
        updateMeta('twitter:description', description.value || 'TeknoNalar - Platform edukasi teknologi.');
        updateMeta('twitter:image', image.value || '/images/logo.png');

        // JSON-LD Schema
        if (schema) {
            let script = document.querySelector('script[type="application/ld+json"]');
            if (!script) {
                script = document.createElement('script');
                script.setAttribute('type', 'application/ld+json');
                document.head.appendChild(script);
            }
            script.textContent = JSON.stringify(schema.value || schema);
        }
    });
}
