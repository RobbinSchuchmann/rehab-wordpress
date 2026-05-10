import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, TextControl, SelectControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

const PhoneSvg = () => (
	<svg width="14" height="14" viewBox="0 0 15 16" aria-hidden="true"><path d="M14.8 13.1c-.8 2-2.8 2.4-3.5 2.4-.2 0-3.2.2-7.4-3.9C.5 8.3 0 4.8 0 4.1 0 3.4.2 1.8 2.4.6c.3-.2.8-.2 1-.1.1.1 1.9 3.2 2 3.3 0 .1.1.2.1.3 0 .1-.1.3-.3.5l-.7.7c-.3.1-.5.3-.7.5-.2.2-.3.4-.3.5 0 .3.3 1.5 2.3 3.3C7.9 11.5 8.9 12 9 12c.1 0 .2.1.2.1.1 0 .3-.1.5-.3.2-.2.9-1.1 1.1-1.3.2-.2.4-.3.5-.3.1 0 .2 0 .3.1.1 0 3.2 1.9 3.3 1.9.2.1.1.6-.1.9" fill="currentColor"/></svg>
);

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Layout" initialOpen>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'White', value: 'white' }, { label: 'Cream', value: 'cream' }, { label: 'Sage mist', value: 'sage-mist' } ] } onChange={ set( 'background' ) } />
					</PanelBody>
					<PanelBody title="Doctor card" initialOpen={ false }>
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { doctorImageUrl: m.url, doctorImageAlt: m.alt || '' } ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>{ a.doctorImageUrl ? 'Replace photo' : 'Pick photo' }</Button> } />
						</MediaUploadCheck>
						<TextControl label="Photo URL" value={ a.doctorImageUrl } onChange={ set( 'doctorImageUrl' ) } />
						<TextControl label="Photo alt" value={ a.doctorImageAlt } onChange={ set( 'doctorImageAlt' ) } />
						<TextControl label="Phone href" value={ a.doctorPhoneHref } onChange={ set( 'doctorPhoneHref' ) } help="If empty, derived from phone display" />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-intro-doctor-card rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<div className="rehab-intro-doctor-card__grid">
								<div>
									<RichText tagName="span" className="rehab-intro-doctor-card__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
									<RichText tagName="h2" className="rehab-intro-doctor-card__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Section heading…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
									<div className="rehab-doctor-card">
										<div className="rehab-doctor-card__avatar">
											{ a.doctorImageUrl ? <img src={ a.doctorImageUrl } alt={ a.doctorImageAlt } /> : null }
										</div>
										<div>
											<RichText tagName="div" className="rehab-doctor-card__label" value={ a.doctorLabel } onChange={ set( 'doctorLabel' ) } placeholder="Speak with…" allowedFormats={ [] } />
											<RichText tagName="p" className="rehab-doctor-card__name" value={ a.doctorName } onChange={ set( 'doctorName' ) } placeholder="Director name" allowedFormats={ [] } />
											<span className="rehab-doctor-card__phone"><PhoneSvg /><RichText tagName="span" value={ a.doctorPhone } onChange={ set( 'doctorPhone' ) } placeholder="+66 …" allowedFormats={ [] } /></span>
										</div>
									</div>
								</div>
								<RichText
									tagName="div"
									className="rehab-intro-doctor-card__copy"
									multiline="p"
									value={ a.body }
									onChange={ set( 'body' ) }
									placeholder="Body paragraphs…"
									allowedFormats={ [ 'core/bold', 'core/italic', 'core/link' ] }
								/>
							</div>
						</div>
					</section>
				</div>
			</>
		);
	},
	save( { attributes } ) {
		const a = attributes;
		const phoneHref = a.doctorPhoneHref || `tel:${ ( a.doctorPhone || '' ).replace( /[^+\d]/g, '' ) }`;
		return (
			<section className={ `rehab-intro-doctor-card rehab-bg-${ a.background }` }>
				<div className="rehab-container">
					<div className="rehab-intro-doctor-card__grid">
						<div>
							<RichText.Content tagName="span" className="rehab-intro-doctor-card__eyebrow" value={ a.eyebrow } />
							<RichText.Content tagName="h2" className="rehab-intro-doctor-card__heading" value={ a.heading } />
							{ ( a.doctorName || a.doctorImageUrl ) ? (
								<div className="rehab-doctor-card">
									<div className="rehab-doctor-card__avatar">
										{ a.doctorImageUrl ? <img src={ a.doctorImageUrl } alt={ a.doctorImageAlt } /> : null }
									</div>
									<div>
										<RichText.Content tagName="div" className="rehab-doctor-card__label" value={ a.doctorLabel } />
										<RichText.Content tagName="p" className="rehab-doctor-card__name" value={ a.doctorName } />
										{ a.doctorPhone ? (
											<a href={ phoneHref } className="rehab-doctor-card__phone">
												<PhoneSvg /><RichText.Content tagName="span" value={ a.doctorPhone } />
											</a>
										) : null }
									</div>
								</div>
							) : null }
						</div>
						<RichText.Content tagName="div" className="rehab-intro-doctor-card__copy" value={ a.body } />
					</div>
				</div>
			</section>
		);
	},
} );
