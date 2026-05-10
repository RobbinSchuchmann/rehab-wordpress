import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, SelectControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

function Markup( { a } ) {
	const items = Array.isArray( a.items ) ? a.items : [];
	return (
		<section className={ `rehab-journey-steps rehab-bg-${ a.background }` }>
			<div className="rehab-container">
				<div className="rehab-journey-steps__head">
					<span className="rehab-journey-steps__eyebrow">{ a.eyebrow }</span>
					<h2 className="rehab-journey-steps__heading">{ a.heading }</h2>
					{ a.subheading ? <p className="rehab-journey-steps__sub">{ a.subheading }</p> : null }
				</div>
				<div className="rehab-journey-steps__grid">
					{ items.map( ( item, i ) => (
						<div className="rehab-journey-step" key={ i }>
							<span className="rehab-journey-step__num">{ item.label || `STEP ${ String( i + 1 ).padStart( 2, '0' ) }` }</span>
							<h4 className="rehab-journey-step__title">{ item.title }</h4>
							<p className="rehab-journey-step__body">{ item.body }</p>
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
		const update = ( i, k, v ) => {
			const next = items.slice();
			next[ i ] = { ...next[ i ], [ k ]: v };
			setAttributes( { items: next } );
		};
		const add = () => setAttributes( { items: [ ...items, { label: '', title: '', body: '' } ] } );
		const remove = ( i ) => setAttributes( { items: items.filter( ( _, j ) => j !== i ) } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Section header" initialOpen>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'Sage mist', value: 'sage-mist' }, { label: 'White', value: 'white' }, { label: 'Cream', value: 'cream' } ] } onChange={ ( v ) => setAttributes( { background: v } ) } />
						<TextControl label="Eyebrow" value={ a.eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } />
						<TextControl label="Heading" value={ a.heading } onChange={ ( v ) => setAttributes( { heading: v } ) } />
						<TextareaControl label="Subheading" value={ a.subheading } onChange={ ( v ) => setAttributes( { subheading: v } ) } />
					</PanelBody>
					<PanelBody title="Steps">
						{ items.map( ( item, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', paddingBottom: '1rem', marginBottom: '1rem' } }>
								<TextControl label="Step label" value={ item.label } onChange={ ( v ) => update( i, 'label', v ) } help="e.g. STEP 01" />
								<TextControl label="Title" value={ item.title } onChange={ ( v ) => update( i, 'title', v ) } />
								<TextareaControl label="Body" value={ item.body } onChange={ ( v ) => update( i, 'body', v ) } />
								<Button variant="link" isDestructive onClick={ () => remove( i ) }>Remove</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ add }>Add step</Button>
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
