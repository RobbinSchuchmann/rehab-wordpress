import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, SelectControl, ToggleControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

const Check = () => (
	<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="2" strokeLinecap="round" aria-hidden="true"><path d="m5 12 5 5L20 7"/></svg>
);

function Card( { title, icon, items } ) {
	return (
		<div className="rehab-signs-card">
			<div className="rehab-signs-card__head">
				<div className="rehab-signs-card__icon" aria-hidden="true">{ icon }</div>
				<h3 className="rehab-signs-card__title">{ title }</h3>
			</div>
			<ul className="rehab-signs-card__list">
				{ items.map( ( item, i ) => (
					<li key={ i }><Check />{ item }</li>
				) ) }
			</ul>
		</div>
	);
}

function Markup( { a } ) {
	const items1 = Array.isArray( a.card1Items ) ? a.card1Items : [];
	const items2 = Array.isArray( a.card2Items ) ? a.card2Items : [];
	const icon1 = ( <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><circle cx="12" cy="12" r="10"/><path d="M12 8v4M12 16h.01"/></svg> );
	const icon2 = ( <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><path d="M3 12h4l3-9 4 18 3-9h4"/></svg> );
	return (
		<section className={ `rehab-signs-grid rehab-bg-${ a.background }` }>
			<div className="rehab-container">
				<div className="rehab-signs-grid__head">
					<span className="rehab-signs-grid__eyebrow">{ a.eyebrow }</span>
					<h2 className="rehab-signs-grid__heading">{ a.heading }</h2>
					{ a.subheading ? <p className="rehab-signs-grid__sub">{ a.subheading }</p> : null }
				</div>
				<div className="rehab-signs-grid__cards">
					<Card title={ a.card1Title } icon={ icon1 } items={ items1 } />
					<Card title={ a.card2Title } icon={ icon2 } items={ items2 } />
				</div>
				{ a.showCta ? (
					<div className="rehab-signs-grid__cta">
						<div className="rehab-signs-grid__cta-text">
							<h3>{ a.ctaTitle }</h3>
							<p>{ a.ctaBody }</p>
						</div>
						<a href={ a.ctaUrl } className="rehab-btn rehab-btn--luxury">{ a.ctaButton }</a>
					</div>
				) : null }
			</div>
		</section>
	);
}

const ListEditor = ( { label, items, onChange } ) => {
	const update = ( i, v ) => {
		const next = items.slice();
		next[ i ] = v;
		onChange( next );
	};
	const remove = ( i ) => onChange( items.filter( ( _, j ) => j !== i ) );
	const add = () => onChange( [ ...items, '' ] );
	return (
		<div>
			<p style={ { fontWeight: 500, margin: '0.5rem 0 0.25rem' } }>{ label }</p>
			{ items.map( ( item, i ) => (
				<div key={ i } style={ { display: 'flex', gap: '0.5rem', marginBottom: '0.25rem' } }>
					<TextControl __next40pxDefaultSize value={ item } onChange={ ( v ) => update( i, v ) } style={ { flex: 1 } } />
					<Button variant="link" isDestructive onClick={ () => remove( i ) }>×</Button>
				</div>
			) ) }
			<Button variant="secondary" onClick={ add }>Add item</Button>
		</div>
	);
};

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const items1 = Array.isArray( a.card1Items ) ? a.card1Items : [];
		const items2 = Array.isArray( a.card2Items ) ? a.card2Items : [];
		return (
			<>
				<InspectorControls>
					<PanelBody title="Header" initialOpen>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'Cream', value: 'cream' }, { label: 'White', value: 'white' }, { label: 'Sage mist', value: 'sage-mist' } ] } onChange={ ( v ) => setAttributes( { background: v } ) } />
						<TextControl label="Eyebrow" value={ a.eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } />
						<TextControl label="Heading" value={ a.heading } onChange={ ( v ) => setAttributes( { heading: v } ) } />
						<TextareaControl label="Subheading" value={ a.subheading } onChange={ ( v ) => setAttributes( { subheading: v } ) } />
					</PanelBody>
					<PanelBody title="Card 1" initialOpen>
						<TextControl label="Title" value={ a.card1Title } onChange={ ( v ) => setAttributes( { card1Title: v } ) } />
						<ListEditor label="Items" items={ items1 } onChange={ ( v ) => setAttributes( { card1Items: v } ) } />
					</PanelBody>
					<PanelBody title="Card 2" initialOpen>
						<TextControl label="Title" value={ a.card2Title } onChange={ ( v ) => setAttributes( { card2Title: v } ) } />
						<ListEditor label="Items" items={ items2 } onChange={ ( v ) => setAttributes( { card2Items: v } ) } />
					</PanelBody>
					<PanelBody title="Dark CTA bar" initialOpen={ false }>
						<ToggleControl label="Show CTA bar" checked={ a.showCta } onChange={ ( v ) => setAttributes( { showCta: v } ) } />
						<TextControl label="Title" value={ a.ctaTitle } onChange={ ( v ) => setAttributes( { ctaTitle: v } ) } />
						<TextareaControl label="Body" value={ a.ctaBody } onChange={ ( v ) => setAttributes( { ctaBody: v } ) } />
						<TextControl label="Button text" value={ a.ctaButton } onChange={ ( v ) => setAttributes( { ctaButton: v } ) } />
						<TextControl label="Button URL" value={ a.ctaUrl } onChange={ ( v ) => setAttributes( { ctaUrl: v } ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<Markup a={ a } />
				</div>
			</>
		);
	},
	save( { attributes } ) {
		return <Markup a={ attributes } />;
	},
} );
