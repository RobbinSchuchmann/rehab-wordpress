import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const reverseClass = a.imageSide === 'right' ? ' rehab-article-row--reverse' : '';
		const aspectClass = a.imageAspect === 'wide' ? ' rehab-article-row__media--wide' : '';
		return (
			<>
				<InspectorControls>
					<PanelBody title="Layout" initialOpen>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'White', value: 'white' }, { label: 'Cream', value: 'cream' }, { label: 'Sage mist', value: 'sage-mist' } ] } onChange={ set( 'background' ) } />
						<SelectControl label="Image side" value={ a.imageSide } options={ [ { label: 'Left', value: 'left' }, { label: 'Right', value: 'right' } ] } onChange={ set( 'imageSide' ) } />
						<SelectControl label="Image aspect" value={ a.imageAspect } options={ [ { label: 'Tall (4/5)', value: 'tall' }, { label: 'Wide (5/4)', value: 'wide' } ] } onChange={ set( 'imageAspect' ) } />
					</PanelBody>
					<PanelBody title="Image" initialOpen={ false }>
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { imageUrl: m.url, imageAlt: m.alt || '' } ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ a.imageUrl ? 'Replace image' : 'Pick image' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="Image URL" value={ a.imageUrl } onChange={ set( 'imageUrl' ) } />
						<TextControl label="Image alt" value={ a.imageAlt } onChange={ set( 'imageAlt' ) } />
					</PanelBody>
					<PanelBody title="CTA buttons" initialOpen={ false }>
						<TextControl label="Primary URL" value={ a.primaryUrl } onChange={ set( 'primaryUrl' ) } />
						<TextControl label="Secondary URL" value={ a.secondaryUrl } onChange={ set( 'secondaryUrl' ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-article-row-section rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<div className={ `rehab-article-row${ reverseClass }` }>
								<div className={ `rehab-article-row__media${ aspectClass }` }>
									{ a.imageUrl ? <img src={ a.imageUrl } alt={ a.imageAlt } /> : <div className="rehab-article-row__media-placeholder"><span>{ a.imageAlt || 'Image' }</span></div> }
								</div>
								<div className="rehab-article-row__text">
									<RichText tagName="span" className="rehab-article-row__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
									<RichText tagName="h2" className="rehab-article-row__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Section heading…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
									<RichText
										tagName="div"
										multiline="p"
										value={ a.body }
										onChange={ set( 'body' ) }
										placeholder="Body paragraphs…"
										allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
									/>
									{ ( a.primaryText || a.secondaryText ) || true ? (
										<div className="rehab-article-row__cta">
											<span className="rehab-btn rehab-btn--luxury"><RichText tagName="span" value={ a.primaryText } onChange={ set( 'primaryText' ) } placeholder="Primary CTA (optional)…" allowedFormats={ [] } /></span>
											<span className="rehab-btn rehab-btn--outline"><RichText tagName="span" value={ a.secondaryText } onChange={ set( 'secondaryText' ) } placeholder="Secondary CTA (optional)…" allowedFormats={ [] } /></span>
										</div>
									) : null }
								</div>
							</div>
						</div>
					</section>
				</div>
			</>
		);
	},
	save( { attributes } ) {
		const a = attributes;
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
							<RichText.Content tagName="span" className="rehab-article-row__eyebrow" value={ a.eyebrow } />
							<RichText.Content tagName="h2" className="rehab-article-row__heading" value={ a.heading } />
							<RichText.Content tagName="div" value={ a.body } />
							{ ( a.primaryText || a.secondaryText ) ? (
								<div className="rehab-article-row__cta">
									{ a.primaryText ? <a href={ a.primaryUrl } className="rehab-btn rehab-btn--luxury"><RichText.Content value={ a.primaryText } /></a> : null }
									{ a.secondaryText ? <a href={ a.secondaryUrl } className="rehab-btn rehab-btn--outline"><RichText.Content value={ a.secondaryText } /></a> : null }
								</div>
							) : null }
						</div>
					</div>
				</div>
			</section>
		);
	},
} );
