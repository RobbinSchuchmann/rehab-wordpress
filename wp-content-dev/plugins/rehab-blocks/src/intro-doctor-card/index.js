import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { PanelBody, TextControl, TextareaControl, SelectControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

const PhoneSvg = () => (
	<svg width="14" height="14" viewBox="0 0 15 16" aria-hidden="true"><path d="M14.8 13.1c-.8 2-2.8 2.4-3.5 2.4-.2 0-3.2.2-7.4-3.9C.5 8.3 0 4.8 0 4.1 0 3.4.2 1.8 2.4.6c.3-.2.8-.2 1-.1.1.1 1.9 3.2 2 3.3 0 .1.1.2.1.3 0 .1-.1.3-.3.5l-.7.7c-.3.1-.5.3-.7.5-.2.2-.3.4-.3.5 0 .3.3 1.5 2.3 3.3C7.9 11.5 8.9 12 9 12c.1 0 .2.1.2.1.1 0 .3-.1.5-.3.2-.2.9-1.1 1.1-1.3.2-.2.4-.3.5-.3.1 0 .2 0 .3.1.1 0 3.2 1.9 3.3 1.9.2.1.1.6-.1.9" fill="currentColor"/></svg>
);

function Markup( { a } ) {
	const paragraphs = ( a.body || '' ).split( /\n\s*\n/ ).map( ( p ) => p.trim() ).filter( Boolean );
	return (
		<section className={ `rehab-intro-doctor-card rehab-bg-${ a.background }` }>
			<div className="rehab-container">
				<div className="rehab-intro-doctor-card__grid">
					<div>
						{ a.eyebrow ? <span className="rehab-intro-doctor-card__eyebrow">{ a.eyebrow }</span> : null }
						<h2 className="rehab-intro-doctor-card__heading">{ a.heading }</h2>
						{ ( a.doctorName || a.doctorImageUrl ) ? (
							<div className="rehab-doctor-card">
								<div className="rehab-doctor-card__avatar">
									{ a.doctorImageUrl ? <img src={ a.doctorImageUrl } alt={ a.doctorImageAlt } /> : null }
								</div>
								<div>
									{ a.doctorLabel ? <div className="rehab-doctor-card__label">{ a.doctorLabel }</div> : null }
									<p className="rehab-doctor-card__name">{ a.doctorName }</p>
									{ a.doctorPhone ? (
										<a href={ a.doctorPhoneHref || `tel:${ ( a.doctorPhone || '' ).replace( /[^+\d]/g, '' ) }` } className="rehab-doctor-card__phone">
											<PhoneSvg />{ a.doctorPhone }
										</a>
									) : null }
								</div>
							</div>
						) : null }
					</div>
					<div className="rehab-intro-doctor-card__copy">
						{ paragraphs.map( ( p, i ) => <p key={ i }>{ p }</p> ) }
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
					<PanelBody title="Section content" initialOpen>
						<SelectControl label="Background" value={ a.background } options={ [ { label: 'White', value: 'white' }, { label: 'Cream', value: 'cream' }, { label: 'Sage mist', value: 'sage-mist' } ] } onChange={ update( 'background' ) } />
						<TextControl label="Eyebrow" value={ a.eyebrow } onChange={ update( 'eyebrow' ) } />
						<TextControl label="Heading" value={ a.heading } onChange={ update( 'heading' ) } />
						<TextareaControl label="Body" help="Separate paragraphs with a blank line" value={ a.body } onChange={ update( 'body' ) } rows={ 8 } />
					</PanelBody>
					<PanelBody title="Doctor / director card" initialOpen={ false }>
						<TextControl label="Image URL" value={ a.doctorImageUrl } onChange={ update( 'doctorImageUrl' ) } />
						<TextControl label="Image alt" value={ a.doctorImageAlt } onChange={ update( 'doctorImageAlt' ) } />
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { doctorImageUrl: m.url, doctorImageAlt: m.alt || '' } ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>Pick image</Button> } />
						</MediaUploadCheck>
						<TextControl label="Label" value={ a.doctorLabel } onChange={ update( 'doctorLabel' ) } help='e.g. "Speak with our Director"' />
						<TextControl label="Name" value={ a.doctorName } onChange={ update( 'doctorName' ) } />
						<TextControl label="Phone display" value={ a.doctorPhone } onChange={ update( 'doctorPhone' ) } />
						<TextControl label="Phone href (optional)" value={ a.doctorPhoneHref } onChange={ update( 'doctorPhoneHref' ) } help="If empty, derived from phone display" />
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
