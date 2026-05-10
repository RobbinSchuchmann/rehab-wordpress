import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, SelectControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

function Markup( { a } ) {
	const items = Array.isArray( a.items ) ? a.items : [];
	return (
		<section className={ `rehab-pillars rehab-bg-${ a.background }` }>
			<div className="rehab-container">
				<div className="rehab-pillars__head">
					<span className="rehab-pillars__eyebrow">{ a.eyebrow }</span>
					<h2 className="rehab-pillars__heading">{ a.heading }</h2>
					{ a.subheading ? <p className="rehab-pillars__sub">{ a.subheading }</p> : null }
				</div>
				<div className="rehab-pillars__grid">
					{ items.map( ( item, i ) => (
						<div className="rehab-pillar" key={ i }>
							<div className="rehab-pillar__icon" aria-hidden="true">
								<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
							</div>
							<span className="rehab-pillar__num">{ item.num }</span>
							<h3 className="rehab-pillar__title">{ item.title }</h3>
							<p className="rehab-pillar__body">{ item.body }</p>
						</div>
					) ) }
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const items = Array.isArray( a.items ) ? a.items : [];
		const updateItem = ( i, k, v ) => {
			const next = items.slice();
			next[ i ] = { ...next[ i ], [ k ]: v };
			setAttributes( { items: next } );
		};
		const addItem = () => setAttributes( { items: [ ...items, { num: '', title: '', body: '' } ] } );
		const removeItem = ( i ) => setAttributes( { items: items.filter( ( _, j ) => j !== i ) } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Pillars" initialOpen>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'Sage mist', value: 'sage-mist' }, { label: 'White', value: 'white' }, { label: 'Cream', value: 'cream' } ] } onChange={ ( v ) => setAttributes( { background: v } ) } />
						<TextControl label="Eyebrow" value={ a.eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } />
						<TextControl label="Heading" value={ a.heading } onChange={ ( v ) => setAttributes( { heading: v } ) } />
						<TextareaControl label="Subheading" value={ a.subheading } onChange={ ( v ) => setAttributes( { subheading: v } ) } />
					</PanelBody>
					<PanelBody title="Pillar items">
						{ items.map( ( item, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', paddingBottom: '1rem', marginBottom: '1rem' } }>
								<TextControl label="Number / eyebrow" value={ item.num } onChange={ ( v ) => updateItem( i, 'num', v ) } />
								<TextControl label="Title" value={ item.title } onChange={ ( v ) => updateItem( i, 'title', v ) } />
								<TextareaControl label="Body" value={ item.body } onChange={ ( v ) => updateItem( i, 'body', v ) } />
								<Button variant="link" isDestructive onClick={ () => removeItem( i ) }>Remove</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ addItem }>Add pillar</Button>
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
