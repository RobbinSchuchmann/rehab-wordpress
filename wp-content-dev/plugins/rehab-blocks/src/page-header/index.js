import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl, TextControl } from '@wordpress/components';
import metadata from './block.json';
import './style.scss';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const blockProps = useBlockProps();
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Page header" initialOpen>
						<SelectControl
							label="Background"
							value={ a.background }
							options={ [ { label: 'White', value: 'white' }, { label: 'Cream', value: 'cream' }, { label: 'Sage mist', value: 'sage-mist' } ] }
							onChange={ set( 'background' ) }
						/>
						<SelectControl
							label="Alignment"
							value={ a.align }
							options={ [ { label: 'Center', value: 'center' }, { label: 'Left', value: 'left' } ] }
							onChange={ set( 'align' ) }
						/>
						<TextControl label="Feature image URL (optional)" value={ a.imageUrl } onChange={ set( 'imageUrl' ) } />
						<TextControl label="Feature image alt" value={ a.imageAlt } onChange={ set( 'imageAlt' ) } />
					</PanelBody>
				</InspectorControls>
				<div { ...blockProps }>
					<section className={ `rehab-page-header rehab-bg-${ a.background }` }>
						<div className="rehab-container">
							<RichText tagName="span" className="rehab-page-header__eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
							<RichText tagName="h1" className="rehab-page-header__heading" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Page title…" allowedFormats={ [] } />
							<RichText tagName="p" className="rehab-page-header__lede" value={ a.lede } onChange={ set( 'lede' ) } placeholder="Lede…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
						</div>
					</section>
				</div>
			</>
		);
	},
	save: () => null,
} );
