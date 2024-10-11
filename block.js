// Ensure this script is not included multiple times
if (typeof wp !== 'undefined' && typeof wp.blocks !== 'undefined' && typeof wp.blocks.registerBlockType !== 'undefined') {
    (() => {
        const { registerBlockType, unregisterBlockType, getBlockType } = wp.blocks;
        const { useState, useEffect, createElement } = wp.element;
        const { Spinner } = wp.components;

        // Check if the block is already registered
        if (getBlockType('random-products/random-products-block')) {
            unregisterBlockType('random-products/random-products-block');
        }

        registerBlockType('random-products/random-products-block', {
            title: 'Random Products Block',
            icon: 'cart',
            category: 'widgets', // Register under the 'widgets' category
            edit: () => {
                const [products, setProducts] = useState([]);
                const [loading, setLoading] = useState(true);

                useEffect(() => {
                    const fetchProducts = async () => {
                        try {
                            const response = await fetch(randomProductsBlock.rest_url, {
                                headers: { 'X-WP-Nonce': randomProductsBlock.nonce }
                            });
                            const data = await response.json();
                            if (Array.isArray(data)) {
                                setProducts(data);
                            } else {
                                console.error('Unexpected response format:', data);
                            }
                        } catch (error) {
                            console.error('Error fetching products:', error);
                        } finally {
                            setLoading(false);
                        }
                    };

                    fetchProducts();
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
            save: () => null, // Dynamic block, content is rendered in PHP
        });
    })();
}