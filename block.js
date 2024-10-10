const { registerBlockType } = wp.blocks;
const { useState, useEffect, createElement } = wp.element;
const { Spinner } = wp.components;

registerBlockType('random-products/random-products-block', {
    title: 'Random Products Block',
    icon: 'cart',
    category: 'common',
    edit: () => {
        const [products, setProducts] = useState([]);
        const [loading, setLoading] = useState(true);

        useEffect(() => {
            fetch(randomProductsBlock.rest_url, {
                headers: { 'X-WP-Nonce': randomProductsBlock.nonce }
            })
            .then(response => response.json())
            .then(data => {
                if (Array.isArray(data)) {
                    setProducts(data);
                } else {
                    console.error('Unexpected response format:', data);
                }
                setLoading(false);
            })
            .catch(error => {
                console.error('Error fetching products:', error);
                setLoading(false);
            });
        }, []);

        if (loading) {
            return createElement(Spinner, null);
        }

        return createElement(
            'div',
            { className: 'random-products-block' },
            products.map((product) =>
                createElement(
                    'div',
                    { key: product.id, className: 'product' },
                    createElement('img', { src: product.images[0].src, alt: product.name }),
                    createElement('h2', null, product.name),
                    createElement('p', { dangerouslySetInnerHTML: { __html: product.price_html } })
                )
            )
        );
    },
    save: () => {
        return null; // Dynamic block, content is rendered in PHP
    },
});