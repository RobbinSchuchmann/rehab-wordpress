import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

const PillarIcon = () => (
	<svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.5" strokeLinecap="round" strokeLinejoin="round" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
);

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
					<PanelBody title="Section" initialOpen>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'Sage mist', value: 'sage-mist' }, { label: 'White', value: 'white' }, { label: 'Cream', value: 'cream' } ] } onChange={ ( v ) => setAttributes( { background: v } ) } />
					</PanelBody>
					<PanelBody title="Pillars" initialOpen>
						{ items.map( ( _item, i ) => (
							<div key={ i } style={ { display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' } }>
								<span>Pillar { i + 1 }</span>
								<Button variant="link" isDestructive onClick={ () => removeItem( i ) }>Remove</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ addItem }>Add pillar</Button>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-pillars rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<div className="rehab-pillars__head">
								<RichText tagName="span" className="rehab-pillars__eyebrow" value={ a.eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Eyebrow…" allowedFormats={ [] } />
								<RichText tagName="h2" className="rehab-pillars__heading" value={ a.heading } onChange={ ( v ) => setAttributes( { heading: v } ) } placeholder="Section heading…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
								<RichText tagName="p" className="rehab-pillars__sub" value={ a.subheading } onChange={ ( v ) => setAttributes( { subheading: v } ) } placeholder="Subheading…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
							</div>
							<div className="rehab-pillars__grid">
								{ items.map( ( item, i ) => (
									<div className="rehab-pillar" key={ i }>
										<div className="rehab-pillar__icon" aria-hidden="true"><PillarIcon /></div>
										<RichText tagName="span" className="rehab-pillar__num" value={ item.num } onChange={ ( v ) => updateItem( i, 'num', v ) } placeholder="01 — Eyebrow" allowedFormats={ [] } />
										<RichText tagName="h3" className="rehab-pillar__title" value={ item.title } onChange={ ( v ) => updateItem( i, 'title', v ) } placeholder="Pillar title" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
										<RichText tagName="p" className="rehab-pillar__body" value={ item.body } onChange={ ( v ) => updateItem( i, 'body', v ) } placeholder="Pillar body…" allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] } />
									</div>
								) ) }
							</div>
						</div>
					</section>
				</div>
			</>
		);
	},
	save( { attributes } ) {
		const a = attributes;
		const items = Array.isArray( a.items ) ? a.items : [];
		return (
			<section className={ `rehab-pillars rehab-bg-${ a.background }` }>
				<div className="rehab-container">
					<div className="rehab-pillars__head">
						<RichText.Content tagName="span" className="rehab-pillars__eyebrow" value={ a.eyebrow } />
						<RichText.Content tagName="h2" className="rehab-pillars__heading" value={ a.heading } />
						<RichText.Content tagName="p" className="rehab-pillars__sub" value={ a.subheading } />
					</div>
					<div className="rehab-pillars__grid">
						{ items.map( ( item, i ) => (
							<div className="rehab-pillar" key={ i }>
								<div className="rehab-pillar__icon" aria-hidden="true"><PillarIcon /></div>
								<RichText.Content tagName="span" className="rehab-pillar__num" value={ item.num } />
								<RichText.Content tagName="h3" className="rehab-pillar__title" value={ item.title } />
								<RichText.Content tagName="p" className="rehab-pillar__body" value={ item.body } />
							</div>
						) ) }
					</div>
				</div>
			</section>
		);
	},
} );
