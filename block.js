const { registerBlockType } = wp.blocks;
const { useState, useEffect } = wp.element;
const { Spinner } = wp.components;
const apiFetch = wp.apiFetch;

registerBlockType('random-products/random-products-block', {
    title: 'Random Products Block',
    icon: 'cart',
    category: 'widgets',
    edit: () => {
        const [products, setProducts] = useState([]);
        const [loading, setLoading] = useState(true);

        useEffect(() => {
            apiFetch({
                path: '/wc/v3/products?per_page=3&orderby=rand',
                headers: { 'X-WP-Nonce': randomProductsBlock.nonce }
            }).then((products) => {
                setProducts(products);
                setLoading(false);
            });
        }, []);

        if (loading) {
            return <Spinner />;
        }

        return (
            <div className="random-products-block">
                {products.map((product) => (
                    <div key={product.id} className="product">
                        <img src={product.images[0].src} alt={product.name} />
                        <h2>{product.name}</h2>
                        <p dangerouslySetInnerHTML={{ __html: product.price_html }} />
                    </div>
                ))}
            </div>
        );
    },
    save: () => {
        return null; // Dynamic block, content is rendered in PHP
    },
});