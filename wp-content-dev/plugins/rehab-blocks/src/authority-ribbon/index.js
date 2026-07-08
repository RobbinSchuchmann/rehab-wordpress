import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, MediaUpload, MediaUploadCheck, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, TextControl, Button } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

function Markup( { a } ) {
	const logos = Array.isArray( a.logos ) ? a.logos : [];
	return (
		<section className="rehab-authority-ribbon">
			<div className="rehab-container">
				<p className="rehab-authority-ribbon__label">{ a.label }</p>
				<div className="rehab-authority-ribbon__logos">
					{ logos.map( ( logo, i ) => (
						logo.tip
							? <span key={ i } className="rehab-authority-ribbon__item" data-tooltip={ logo.tip }><img src={ logo.url } alt={ logo.alt || '' } /></span>
							: <img key={ i } src={ logo.url } alt={ logo.alt || '' } />
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
		const logos = Array.isArray( a.logos ) ? a.logos : [];
		const updateLogo = ( i, key, value ) => {
			const next = logos.slice();
			next[ i ] = { ...next[ i ], [ key ]: value };
			setAttributes( { logos: next } );
		};
		const addLogo = () => setAttributes( { logos: [ ...logos, { url: '', alt: '' } ] } );
		const removeLogo = ( i ) => setAttributes( { logos: logos.filter( ( _, j ) => j !== i ) } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Authority ribbon" initialOpen>
						<TextControl label="Label" value={ a.label } onChange={ ( v ) => setAttributes( { label: v } ) } />
					</PanelBody>
					<PanelBody title="Logos" initialOpen>
						{ logos.map( ( logo, i ) => (
							<div key={ i } style={ { borderBottom: '1px solid #e5e5e5', paddingBottom: '1rem', marginBottom: '1rem' } }>
								<TextControl label={ `Logo ${ i + 1 } URL` } value={ logo.url } onChange={ ( v ) => updateLogo( i, 'url', v ) } />
								<TextControl label="Alt text" value={ logo.alt } onChange={ ( v ) => updateLogo( i, 'alt', v ) } />
								<MediaUploadCheck>
									<MediaUpload onSelect={ ( m ) => { updateLogo( i, 'url', m.url ); updateLogo( i, 'alt', m.alt || '' ); } } allowedTypes={ [ 'image' ] } render={ ( { open } ) => <Button variant="secondary" onClick={ open } style={ { marginRight: '0.5rem' } }>Pick image</Button> } />
								</MediaUploadCheck>
								<Button variant="link" isDestructive onClick={ () => removeLogo( i ) }>Remove</Button>
							</div>
						) ) }
						<Button variant="primary" onClick={ addLogo }>Add logo</Button>
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
