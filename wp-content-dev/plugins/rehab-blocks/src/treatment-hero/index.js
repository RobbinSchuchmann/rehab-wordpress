import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, MediaUpload, MediaUploadCheck, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

function HeroMarkup( { a } ) {
	return (
		<section className="rehab-treatment-hero">
			<div className="rehab-container">
				<div className="rehab-treatment-hero__grid">
					<div>
						<p className="rehab-treatment-hero__eyebrow"><span className="diamond" aria-hidden="true">◆</span>{ a.eyebrow }</p>
						<h1 className="rehab-treatment-hero__h1">{ a.headline }</h1>
						<p className="rehab-treatment-hero__lede">{ a.lede }</p>
						<div className="rehab-treatment-hero__cta-row">
							<a href={ a.primaryUrl } className="rehab-btn rehab-btn--luxury">{ a.primaryText }</a>
							<a href={ a.secondaryUrl } className="rehab-btn rehab-btn--outline">{ a.secondaryText }</a>
						</div>
						<p className="rehab-treatment-hero__helper"><span className="dot" aria-hidden="true" />{ a.helper }</p>
						<div className="rehab-treatment-hero__trust">
							{ [ 1, 2, 3 ].map( ( i ) => (
								<div className="rehab-treatment-hero__trust-item" key={ i }>
									<div className="num">{ a[ `stat${ i }Num` ] }</div>
									<div className="lbl">{ a[ `stat${ i }Label` ] }</div>
								</div>
							) ) }
						</div>
					</div>
					<div className="rehab-treatment-hero__media">
						<div className="rehab-treatment-hero__image-wrap">
							{ a.imageUrl ? <img src={ a.imageUrl } alt={ a.imageAlt } /> : null }
							<div className="rehab-treatment-hero__image-overlay" aria-hidden="true" />
						</div>
						{ ( a.badgeTitle || a.badgeImageUrl ) ? (
							<div className="rehab-treatment-hero__badge">
								{ a.badgeImageUrl ? <img src={ a.badgeImageUrl } alt="" /> : null }
								<div className="rehab-treatment-hero__badge-text">
									<strong>{ a.badgeTitle }</strong>
									{ a.badgeText }
								</div>
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
					<PanelBody title="Hero copy" initialOpen>
						<TextControl label="Eyebrow" value={ a.eyebrow } onChange={ update( 'eyebrow' ) } />
						<TextControl label="Headline" value={ a.headline } onChange={ update( 'headline' ) } />
						<TextControl label="Lede" value={ a.lede } onChange={ update( 'lede' ) } />
						<TextControl label="Helper text" value={ a.helper } onChange={ update( 'helper' ) } />
					</PanelBody>
					<PanelBody title="CTAs" initialOpen={ false }>
						<TextControl label="Primary text" value={ a.primaryText } onChange={ update( 'primaryText' ) } />
						<TextControl label="Primary URL" value={ a.primaryUrl } onChange={ update( 'primaryUrl' ) } />
						<TextControl label="Secondary text" value={ a.secondaryText } onChange={ update( 'secondaryText' ) } />
						<TextControl label="Secondary URL" value={ a.secondaryUrl } onChange={ update( 'secondaryUrl' ) } />
					</PanelBody>
					<PanelBody title="Trust stats" initialOpen={ false }>
						{ [ 1, 2, 3 ].map( ( i ) => (
							<div key={ i } style={ { marginBottom: '1rem' } }>
								<TextControl label={ `Stat ${ i } number` } value={ a[ `stat${ i }Num` ] } onChange={ update( `stat${ i }Num` ) } />
								<TextControl label={ `Stat ${ i } label` } value={ a[ `stat${ i }Label` ] } onChange={ update( `stat${ i }Label` ) } />
							</div>
						) ) }
					</PanelBody>
					<PanelBody title="Image + badge" initialOpen={ false }>
						<TextControl label="Image URL" value={ a.imageUrl } onChange={ update( 'imageUrl' ) } />
						<TextControl label="Image alt" value={ a.imageAlt } onChange={ update( 'imageAlt' ) } />
						<MediaUploadCheck>
							<MediaUpload onSelect={ ( m ) => setAttributes( { imageUrl: m.url, imageAlt: m.alt || '' } ) } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open }>Pick image</Button> } />
						</MediaUploadCheck>
						<TextControl label="Badge image URL" value={ a.badgeImageUrl } onChange={ update( 'badgeImageUrl' ) } />
						<TextControl label="Badge title" value={ a.badgeTitle } onChange={ update( 'badgeTitle' ) } />
						<TextControl label="Badge text" value={ a.badgeText } onChange={ update( 'badgeText' ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<HeroMarkup a={ a } />
				</div>
			</>
		);
	},
	save( { attributes } ) {
		return <HeroMarkup a={ attributes } />;
	},
} );
