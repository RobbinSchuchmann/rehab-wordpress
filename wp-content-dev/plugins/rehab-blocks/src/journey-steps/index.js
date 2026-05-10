import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

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
					<PanelBody title="Section" initialOpen>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'Sage mist', value: 'sage-mist' }, { label: 'White', value: 'white' }, { label: 'Cream', value: 'cream' } ] } onChange={ ( v ) => setAttributes( { background: v } ) } />
					</PanelBody>
					<PanelBody title="Steps" initialOpen>
						{ items.map( ( _item, i ) => (
							<div key={ i } style={ { display: 'flex', justifyContent: 'space-between', marginBottom: '0.5rem' } }>
								<span>Step { i + 1 }</span>
								<Button variant="link" isDestructive onClick={ () => remove( i ) }>Remove</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ add }>Add step</Button>
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-journey-steps rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<div className="rehab-journey-steps__head">
								<RichText tagName="span" className="rehab-journey-steps__eyebrow" value={ a.eyebrow } onChange={ ( v ) => setAttributes( { eyebrow: v } ) } placeholder="Eyebrow…" allowedFormats={ [] } />
								<RichText tagName="h2" className="rehab-journey-steps__heading" value={ a.heading } onChange={ ( v ) => setAttributes( { heading: v } ) } placeholder="Section heading…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
								<RichText tagName="p" className="rehab-journey-steps__sub" value={ a.subheading } onChange={ ( v ) => setAttributes( { subheading: v } ) } placeholder="Subheading…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
							</div>
							<div className="rehab-journey-steps__grid">
								{ items.map( ( item, i ) => (
									<div className="rehab-journey-step" key={ i }>
										<RichText tagName="span" className="rehab-journey-step__num" value={ item.label || `STEP ${ String( i + 1 ).padStart( 2, '0' ) }` } onChange={ ( v ) => update( i, 'label', v ) } placeholder="STEP 01" allowedFormats={ [] } />
										<RichText tagName="h4" className="rehab-journey-step__title" value={ item.title } onChange={ ( v ) => update( i, 'title', v ) } placeholder="Step title" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
										<RichText tagName="p" className="rehab-journey-step__body" value={ item.body } onChange={ ( v ) => update( i, 'body', v ) } placeholder="Step body…" allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] } />
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
			<section className={ `rehab-journey-steps rehab-bg-${ a.background }` }>
				<div className="rehab-container">
					<div className="rehab-journey-steps__head">
						<RichText.Content tagName="span" className="rehab-journey-steps__eyebrow" value={ a.eyebrow } />
						<RichText.Content tagName="h2" className="rehab-journey-steps__heading" value={ a.heading } />
						<RichText.Content tagName="p" className="rehab-journey-steps__sub" value={ a.subheading } />
					</div>
					<div className="rehab-journey-steps__grid">
						{ items.map( ( item, i ) => (
							<div className="rehab-journey-step" key={ i }>
								<RichText.Content tagName="span" className="rehab-journey-step__num" value={ item.label || `STEP ${ String( i + 1 ).padStart( 2, '0' ) }` } />
								<RichText.Content tagName="h4" className="rehab-journey-step__title" value={ item.title } />
								<RichText.Content tagName="p" className="rehab-journey-step__body" value={ item.body } />
							</div>
						) ) }
					</div>
				</div>
			</section>
		);
	},
} );
