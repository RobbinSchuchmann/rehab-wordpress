import { registerBlockType } from '@wordpress/blocks';
import { useBlockProps, RichText, InspectorControls } from '@wordpress/block-editor';
import { PanelBody, SelectControl } from '@wordpress/components';
import metadata from './block.json';

registerBlockType( metadata.name, {
	edit( { attributes, setAttributes } ) {
		const a = attributes;
		const set = ( k ) => ( v ) => setAttributes( { [ k ]: v } );
		const blockProps = useBlockProps( { className: `drt-approach drt-bg-${ a.background }` } );
		return (
			<>
				<InspectorControls>
					<PanelBody title="Background" initialOpen>
						<SelectControl
							label="Background"
							value={ a.background }
							options={ [
								{ label: 'Sage mist', value: 'sage-mist' },
								{ label: 'White', value: 'white' },
								{ label: 'Cream', value: 'cream' },
							] }
							onChange={ set( 'background' ) }
						/>
					</PanelBody>
				</InspectorControls>
				<section { ...blockProps }>
					<div className="drt-container">
						<div className="drt-approach__inner">
							<RichText tagName="span" className="drt-eyebrow" value={ a.eyebrow } onChange={ set( 'eyebrow' ) } placeholder="Eyebrow…" allowedFormats={ [] } />
							<RichText tagName="h2" className="drt-heading drt-heading--lg drt-approach__title" value={ a.heading } onChange={ set( 'heading' ) } placeholder="Heading…" allowedFormats={ [] } />
							<RichText tagName="p" className="drt-body drt-approach__text" value={ a.body } onChange={ set( 'body' ) } placeholder="Body…" allowedFormats={ [ 'core/bold', 'core/italic' ] } />
						</div>
					</div>
				</section>
			</>
		);
	},
	save: () => null,
} );
