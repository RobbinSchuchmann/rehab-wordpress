import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, SelectControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

function Markup( { a } ) {
	const paragraphs = ( a.body || '' ).split( /\n\s*\n/ ).map( ( p ) => p.trim() ).filter( Boolean );
	const reverseClass = a.imageSide === 'right' ? ' rehab-article-row--reverse' : '';
	const aspectClass = a.imageAspect === 'wide' ? ' rehab-article-row__media--wide' : '';
	return (
		<section className={ `rehab-article-row-section rehab-bg-${ a.background }` }>
			<div className="rehab-container">
				<div className={ `rehab-article-row${ reverseClass }` }>
					<div className={ `rehab-article-row__media${ aspectClass }` }>
						{ a.imageUrl ? <img src={ a.imageUrl } alt={ a.imageAlt } /> : <div className="rehab-article-row__media-placeholder"><span>{ a.imageAlt || 'Image' }</span></div> }
					</div>
					<div className="rehab-article-row__text">
						{ a.eyebrow ? <span className="rehab-article-row__eyebrow">{ a.eyebrow }</span> : null }
						<h2 className="rehab-article-row__heading">{ a.heading }</h2>
						{ paragraphs.map( ( p, i ) => <p key={ i }>{ p }</p> ) }
						{ ( a.primaryText || a.secondaryText ) ? (
							<div className="rehab-article-row__cta">
								{ a.primaryText ? <a href={ a.primaryUrl } className="rehab-btn rehab-btn--luxury">{ a.primaryText }</a> : null }
								{ a.secondaryText ? <a href={ a.secondaryUrl } className="rehab-btn rehab-btn--outline">{ a.secondaryText }</a> : null }
							</div>
						) : null }
					</div>
				</div>
			</div>
		</section>
	);
}

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const update = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Layout" initialOpen>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'White', value: 'white' }, { label: 'Cream', value: 'cream' }, { label: 'Sage mist', value: 'sage-mist' } ] } onChange={ update( 'background' ) } />
						<SelectControl label="Image side" value={ a.imageSide } options={ [ { label: 'Left', value: 'left' }, { label: 'Right', value: 'right' } ] } onChange={ update( 'imageSide' ) } />
						<SelectControl label="Image aspect" value={ a.imageAspect } options={ [ { label: 'Tall (4/5)', value: 'tall' }, { label: 'Wide (5/4)', value: 'wide' } ] } onChange={ update( 'imageAspect' ) } />
					</PanelBody>
					<PanelBody title="Image" initialOpen>
						<TextControl label="Image URL" value={ a.imageUrl } onChange={ update( 'imageUrl' ) } />
						<TextControl label="Image alt" value={ a.imageAlt } onChange={ update( 'imageAlt' ) } />
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { imageUrl: m.url, imageAlt: m.alt || '' } ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>Pick image</Button> } />
						</MediaUploadCheck>
					</PanelBody>
					<PanelBody title="Text" initialOpen>
						<TextControl label="Eyebrow" value={ a.eyebrow } onChange={ update( 'eyebrow' ) } />
						<TextControl label="Heading" value={ a.heading } onChange={ update( 'heading' ) } />
						<TextareaControl label="Body" help="Separate paragraphs with a blank line" value={ a.body } onChange={ update( 'body' ) } rows={ 6 } />
					</PanelBody>
					<PanelBody title="CTA buttons" initialOpen={ false }>
						<TextControl label="Primary text" value={ a.primaryText } onChange={ update( 'primaryText' ) } />
						<TextControl label="Primary URL" value={ a.primaryUrl } onChange={ update( 'primaryUrl' ) } />
						<TextControl label="Secondary text" value={ a.secondaryText } onChange={ update( 'secondaryText' ) } />
						<TextControl label="Secondary URL" value={ a.secondaryUrl } onChange={ update( 'secondaryUrl' ) } />
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
